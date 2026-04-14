<?php

namespace App\Http\Controllers\Api;

use App\Models\Table;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TableController extends Controller
{
    public function index()
    {
        return response()->json(Table::query()->latest()->get());
    }

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

    public function show(Table $table)
    {
        $table->load('orders.items');

        return response()->json($table);
    }

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

    public function destroy(Table $table)
    {
        $table->delete();

        return response()->json(status: 204);
    }
}
