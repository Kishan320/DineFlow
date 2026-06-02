<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 100])) {
            $perPage = 10;
        }

        $search = trim($request->get('search', ''));
        $page = (int) $request->get('page', 1);

        $query = RestaurantTable::query()
            ->select(['id', 'table_name', 'description', 'max_seats', 'last_accessed_by', 'updated_at']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('table_name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('last_accessed_by', 'LIKE', "%{$search}%");
            });
        }

        $query->orderByDesc('id');
        $tables = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success'      => true,
            'message'      => 'Tables fetched successfully',
            'data'         => $tables->items(),
            'current_page' => $tables->currentPage(),
            'per_page'     => $tables->perPage(),
            'total'        => $tables->total(),
            'last_page'    => $tables->lastPage(),
            'from'         => $tables->firstItem(),
            'to'           => $tables->lastItem(),
        ]);
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
