<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class OrderItemController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/order-items",
     *     tags={"Order Items"},
     *     summary="List order items",
     *     @OA\Response(
     *         response=200,
     *         description="Order items fetched successfully",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/OrderItem"))
     *     )
     * )
     */
    public function index()
    {
        return response()->json(
            OrderItem::query()->with(['menuItem', 'order'])->latest()->get()
        );
    }

    /**
     * @OA\Post(
     *     path="/api/order-items",
     *     tags={"Order Items"},
     *     summary="Create an order item",
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/OrderItemRequest")),
     *     @OA\Response(response=201, description="Order item created successfully", @OA\JsonContent(ref="#/components/schemas/OrderItem")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'menu_item_id' => ['required', 'exists:menu_items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        $menuItem = MenuItem::findOrFail($validated['menu_item_id']);
        $unitPrice = (float) $menuItem->price;

        $orderItem = OrderItem::create([
            'order_id' => $validated['order_id'],
            'menu_item_id' => $validated['menu_item_id'],
            'quantity' => $validated['quantity'],
            'unit_price' => $unitPrice,
            'line_total' => $unitPrice * $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->recalculateOrderTotals($orderItem->order()->firstOrFail());

        return response()->json($orderItem->load(['menuItem', 'order']), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/order-items/{order_item}",
     *     tags={"Order Items"},
     *     summary="Get a single order item",
     *     @OA\Parameter(name="order_item", in="path", required=true, description="Order item ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Order item fetched successfully", @OA\JsonContent(ref="#/components/schemas/OrderItem"))
     * )
     */
    public function show(OrderItem $orderItem)
    {
        return response()->json($orderItem->load(['menuItem', 'order']));
    }

    /**
     * @OA\Put(
     *     path="/api/order-items/{order_item}",
     *     tags={"Order Items"},
     *     summary="Update an order item",
     *     @OA\Parameter(name="order_item", in="path", required=true, description="Order item ID", @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/OrderItemRequest")),
     *     @OA\Response(response=200, description="Order item updated successfully", @OA\JsonContent(ref="#/components/schemas/OrderItem")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function update(Request $request, OrderItem $orderItem)
    {
        $validated = $request->validate([
            'menu_item_id' => ['sometimes', 'required', 'exists:menu_items,id'],
            'quantity' => ['sometimes', 'required', 'integer', 'min:1'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ]);

        $menuItemId = $validated['menu_item_id'] ?? $orderItem->menu_item_id;
        $menuItem = MenuItem::findOrFail($menuItemId);
        $unitPrice = (float) $menuItem->price;
        $quantity = (int) ($validated['quantity'] ?? $orderItem->quantity);

        $orderItem->update([
            'menu_item_id' => $menuItem->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $unitPrice * $quantity,
            'notes' => array_key_exists('notes', $validated) ? $validated['notes'] : $orderItem->notes,
        ]);

        $this->recalculateOrderTotals($orderItem->order()->firstOrFail());

        return response()->json($orderItem->fresh()->load(['menuItem', 'order']));
    }

    /**
     * @OA\Delete(
     *     path="/api/order-items/{order_item}",
     *     tags={"Order Items"},
     *     summary="Delete an order item",
     *     @OA\Parameter(name="order_item", in="path", required=true, description="Order item ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=204, description="Order item deleted successfully")
     * )
     */
    public function destroy(OrderItem $orderItem)
    {
        $order = $orderItem->order()->firstOrFail();
        $orderItem->delete();
        $this->recalculateOrderTotals($order);

        return response()->json(status: 204);
    }

    private function recalculateOrderTotals(Order $order): void
    {
        $subtotal = (float) $order->items()->sum('line_total');
        $tax = (float) $order->tax_amount;

        $order->update([
            'subtotal' => $subtotal,
            'total' => $subtotal + $tax,
        ]);
    }
}
