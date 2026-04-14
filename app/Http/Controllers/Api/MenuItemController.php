<?php

namespace App\Http\Controllers\Api;

use App\Models\MenuItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuItem::query();

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('is_available')) {
            $query->where('is_available', filter_var($request->input('is_available'), FILTER_VALIDATE_BOOLEAN));
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'short_code' => ['nullable', 'string', 'max:40', 'unique:menu_items,short_code'],
            'item_type' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_available' => ['sometimes', 'boolean'],
        ]);

        $menuItem = MenuItem::create($validated);

        return response()->json($menuItem, 201);
    }

    public function show(MenuItem $menuItem)
    {
        $menuItem->load('orderItems');

        return response()->json($menuItem);
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'category' => ['sometimes', 'required', 'string', 'max:255'],
            'short_code' => ['sometimes', 'nullable', 'string', 'max:40', Rule::unique('menu_items', 'short_code')->ignore($menuItem->id)],
            'item_type' => ['sometimes', 'required', 'string', 'max:255'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'is_available' => ['sometimes', 'boolean'],
        ]);

        $menuItem->update($validated);

        return response()->json($menuItem->fresh());
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();

        return response()->json(status: 204);
    }
}
