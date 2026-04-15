<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class TableController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tables",
     *     tags={"Tables"},
     *     summary="List dining tables",
     *     @OA\Response(
     *         response=200,
     *         description="Dining tables fetched successfully",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Table"))
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Table::query()->latest()->get());
    }

    /**
     * @OA\Post(
     *     path="/api/tables",
     *     tags={"Tables"},
     *     summary="Create a dining table",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TableRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Dining table created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Table")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:dining_tables,name'],
            'floor' => ['nullable', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $table = Table::create($validated);

        return response()->json($table, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/tables/{table}",
     *     tags={"Tables"},
     *     summary="Get a single dining table",
     *     @OA\Parameter(name="table", in="path", required=true, description="Table ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Dining table fetched successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Table")
     *     )
     * )
     */
    public function show(Table $table)
    {
        $table->load('orders.items');

        return response()->json($table);
    }

    /**
     * @OA\Put(
     *     path="/api/tables/{table}",
     *     tags={"Tables"},
     *     summary="Update a dining table",
     *     @OA\Parameter(name="table", in="path", required=true, description="Table ID", @OA\Schema(type="integer", example=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TableRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dining table updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Table")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('dining_tables', 'name')->ignore($table->id)],
            'floor' => ['sometimes', 'nullable', 'string', 'max:255'],
            'capacity' => ['sometimes', 'required', 'integer', 'min:1'],
            'status' => ['sometimes', 'required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $table->update($validated);

        return response()->json($table->fresh());
    }

    /**
     * @OA\Delete(
     *     path="/api/tables/{table}",
     *     tags={"Tables"},
     *     summary="Delete a dining table",
     *     @OA\Parameter(name="table", in="path", required=true, description="Table ID", @OA\Schema(type="integer", example=1)),
     *     @OA\Response(response=204, description="Dining table deleted successfully")
     * )
     */
    public function destroy(Table $table)
    {
        $table->delete();

        return response()->json(status: 204);
    }
}
