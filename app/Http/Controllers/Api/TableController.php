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
        $request->validate([
            'table_name'  => 'required|string|max:100|unique:restaurant_tables,table_name',
            'description' => 'nullable|string|max:255',
            'max_seats'   => 'required|integer|min:1',
        ]);

        $table = new RestaurantTable();
        $table->table_name      = $request->table_name;
        $table->description     = $request->description;
        $table->max_seats       = $request->max_seats;
        $table->last_accessed_by = 'Administrator';
        $table->save();
        $table->refresh();
        return response()->json($table, 201);
    }

    public function update(Request $request, RestaurantTable $table)
    {
        $request->validate([
            'table_name'  => 'required|string|max:100|unique:restaurant_tables,table_name,' . $table->id,
            'description' => 'nullable|string|max:255',
            'max_seats'   => 'required|integer|min:1',
        ]);

        $table->table_name       = $request->table_name;
        $table->description      = $request->description;
        $table->max_seats        = $request->max_seats;
        $table->last_accessed_by = 'Administrator';
        $table->save();
        $table->refresh();
        return response()->json($table);
    }

    public function destroy(RestaurantTable $table)
    {
        $table->delete();
        return response()->json(null, 204);
    }
}
