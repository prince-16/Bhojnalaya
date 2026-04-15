<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class MenuItemController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/menu-items",
     *     tags={"Menu Items"},
     *     summary="List menu items",
     *     @OA\Parameter(name="category", in="query", required=false, @OA\Schema(type="string", example="Starters")),
     *     @OA\Parameter(name="is_available", in="query", required=false, @OA\Schema(type="boolean", example=true)),
     *     @OA\Response(
     *         response=200,
     *         description="Menu items fetched successfully",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/MenuItem"))
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/menu-items",
     *     tags={"Menu Items"},
     *     summary="Create a menu item",
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/MenuItemRequest")),
     *     @OA\Response(response=201, description="Menu item created successfully", @OA\JsonContent(ref="#/components/schemas/MenuItem")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/menu-items/{menu_item}",
     *     tags={"Menu Items"},
     *     summary="Get a single menu item",
     *     @OA\Parameter(name="menu_item", in="path", required=true, description="Menu item ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=200, description="Menu item fetched successfully", @OA\JsonContent(ref="#/components/schemas/MenuItem"))
     * )
     */
    public function show(MenuItem $menuItem)
    {
        $menuItem->load('orderItems');

        return response()->json($menuItem);
    }

    /**
     * @OA\Put(
     *     path="/api/menu-items/{menu_item}",
     *     tags={"Menu Items"},
     *     summary="Update a menu item",
     *     @OA\Parameter(name="menu_item", in="path", required=true, description="Menu item ID", @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/MenuItemRequest")),
     *     @OA\Response(response=200, description="Menu item updated successfully", @OA\JsonContent(ref="#/components/schemas/MenuItem")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/menu-items/{menu_item}",
     *     tags={"Menu Items"},
     *     summary="Delete a menu item",
     *     @OA\Parameter(name="menu_item", in="path", required=true, description="Menu item ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=204, description="Menu item deleted successfully")
     * )
     */
    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();

        return response()->json(status: 204);
    }
}
