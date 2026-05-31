<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        return response()->json(RestaurantTable::orderBy('table_name')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'table_name'  => 'required|string|max:100|unique:restaurant_tables,table_name',
            'description' => 'nullable|string|max:255',
            'max_seats'   => 'required|integer|min:1',
        ]);

        $data['last_accessed_by'] = 'Administrator';
        return response()->json(RestaurantTable::create($data), 201);
    }

    public function update(Request $request, RestaurantTable $table)
    {
        $data = $request->validate([
            'table_name'  => 'required|string|max:100|unique:restaurant_tables,table_name,' . $table->id,
            'description' => 'nullable|string|max:255',
            'max_seats'   => 'required|integer|min:1',
        ]);

        $data['last_accessed_by'] = 'Administrator';
        $table->update($data);
        return response()->json($table);
    }

    public function destroy(RestaurantTable $table)
    {
        $table->delete();
        return response()->json(null, 204);
    }
}
