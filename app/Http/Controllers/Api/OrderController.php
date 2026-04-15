<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="List orders",
     *     @OA\Response(
     *         response=200,
     *         description="Orders fetched successfully",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))
     *     )
     * )
     */
    public function index()
    {
        $orders = Order::query()
            ->with(['table', 'items.menuItem'])
            ->latest()
            ->get();

        return response()->json($orders);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     tags={"Orders"},
     *     summary="Create an order with optional items",
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/OrderRequest")),
     *     @OA\Response(response=201, description="Order created successfully", @OA\JsonContent(ref="#/components/schemas/Order")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_id' => ['nullable', 'exists:dining_tables,id'],
            'order_number' => ['required', 'string', 'max:100', 'unique:orders,order_number'],
            'order_type' => ['required', 'string', 'max:100'],
            'status' => ['sometimes', 'string', 'max:100'],
            'tax_amount' => ['sometimes', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['sometimes', 'array'],
            'items.*.menu_item_id' => ['required_with:items', 'exists:menu_items,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        $order = DB::transaction(function () use ($validated) {
            $order = Order::create([
                'table_id' => $validated['table_id'] ?? null,
                'order_number' => $validated['order_number'],
                'order_type' => $validated['order_type'],
                'status' => $validated['status'] ?? 'open',
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            if (!empty($validated['items'])) {
                $menuItems = MenuItem::query()->whereIn('id', collect($validated['items'])->pluck('menu_item_id'))->get()->keyBy('id');

                foreach ($validated['items'] as $itemData) {
                    $menuItem = $menuItems[$itemData['menu_item_id']];
                    $quantity = (int) $itemData['quantity'];
                    $unitPrice = (float) $menuItem->price;

                    $order->items()->create([
                        'menu_item_id' => $menuItem->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'line_total' => $unitPrice * $quantity,
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }
            }

            $this->recalculateTotals($order);

            return $order->load(['table', 'items.menuItem']);
        });

        return response()->json($order, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{order}",
     *     tags={"Orders"},
     *     summary="Get a single order",
     *     @OA\Parameter(name="order", in="path", required=true, description="Order ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Order fetched successfully", @OA\JsonContent(ref="#/components/schemas/Order"))
     * )
     */
    public function show(Order $order)
    {
        return response()->json($order->load(['table', 'items.menuItem']));
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{order}",
     *     tags={"Orders"},
     *     summary="Update an order and replace its items",
     *     @OA\Parameter(name="order", in="path", required=true, description="Order ID", @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/OrderRequest")),
     *     @OA\Response(response=200, description="Order updated successfully", @OA\JsonContent(ref="#/components/schemas/Order")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'table_id' => ['sometimes', 'nullable', 'exists:dining_tables,id'],
            'order_number' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('orders', 'order_number')->ignore($order->id)],
            'order_type' => ['sometimes', 'required', 'string', 'max:100'],
            'status' => ['sometimes', 'string', 'max:100'],
            'tax_amount' => ['sometimes', 'numeric', 'min:0'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'items' => ['sometimes', 'array'],
            'items.*.menu_item_id' => ['required_with:items', 'exists:menu_items,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string'],
        ]);

        $order = DB::transaction(function () use ($order, $validated) {
            $order->update(collect($validated)->except(['items'])->toArray());

            if (array_key_exists('items', $validated)) {
                $order->items()->delete();

                $menuItems = MenuItem::query()->whereIn('id', collect($validated['items'])->pluck('menu_item_id'))->get()->keyBy('id');

                foreach ($validated['items'] as $itemData) {
                    $menuItem = $menuItems[$itemData['menu_item_id']];
                    $quantity = (int) $itemData['quantity'];
                    $unitPrice = (float) $menuItem->price;

                    $order->items()->create([
                        'menu_item_id' => $menuItem->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'line_total' => $unitPrice * $quantity,
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }
            }

            $this->recalculateTotals($order);

            return $order->load(['table', 'items.menuItem']);
        });

        return response()->json($order);
    }

    /**
     * @OA\Delete(
     *     path="/api/orders/{order}",
     *     tags={"Orders"},
     *     summary="Delete an order",
     *     @OA\Parameter(name="order", in="path", required=true, description="Order ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=204, description="Order deleted successfully")
     * )
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json(status: 204);
    }

    private function recalculateTotals(Order $order): void
    {
        $subtotal = (float) $order->items()->sum('line_total');
        $tax = (float) $order->tax_amount;

        $order->update([
            'subtotal' => $subtotal,
            'total' => $subtotal + $tax,
        ]);
    }
}
