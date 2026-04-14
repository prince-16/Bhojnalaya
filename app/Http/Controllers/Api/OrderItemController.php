<?php

namespace App\Http\Controllers\Api;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function index()
    {
        return response()->json(
            OrderItem::query()->with(['menuItem', 'order'])->latest()->get()
        );
    }

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

    public function show(OrderItem $orderItem)
    {
        return response()->json($orderItem->load(['menuItem', 'order']));
    }

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
