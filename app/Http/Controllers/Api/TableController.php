<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TableController extends Controller
{
    public function index(Request $request)
    {
        $userId  = auth()->id();
        $perPage = in_array((int)$request->per_page, [10, 25, 50, 100]) ? (int)$request->per_page : 10;
        $search  = trim($request->get('search', ''));

        $query = RestaurantTable::forUser($userId)
            ->select(['id', 'table_name', 'description', 'max_seats', 'last_accessed_by', 'updated_at'])
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('table_name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            }))
            ->orderByDesc('id');

        $tables = $query->paginate($perPage, ['*'], 'page', (int)$request->get('page', 1));

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
        $userId = auth()->id();
        $request->validate([
            'table_name'  => ['required', 'string', 'max:100', Rule::unique('restaurant_tables')->where('created_by', $userId)],
            'description' => 'nullable|string|max:255',
            'max_seats'   => 'required|integer|min:1',
        ]);

        $table = RestaurantTable::create([
            'created_by'       => $userId,
            'table_name'       => $request->table_name,
            'description'      => $request->description,
            'max_seats'        => $request->max_seats,
            'last_accessed_by' => auth()->user()->name,
        ]);

        return response()->json($table->fresh(), 201);
    }

    public function update(Request $request, $id)
    {
        $userId = auth()->id();
        $table  = RestaurantTable::forUser($userId)->findOrFail($id);

        $request->validate([
            'table_name'  => ['required', 'string', 'max:100', Rule::unique('restaurant_tables')->where('created_by', $userId)->ignore($table->id)],
            'description' => 'nullable|string|max:255',
            'max_seats'   => 'required|integer|min:1',
        ]);

        $table->update([
            'table_name'       => $request->table_name,
            'description'      => $request->description,
            'max_seats'        => $request->max_seats,
            'last_accessed_by' => auth()->user()->name,
        ]);

        return response()->json($table->fresh());
    }

    public function destroy($id)
    {
        $table = RestaurantTable::forUser(auth()->id())->findOrFail($id);
        $table->delete();
        return response()->json(null, 204);
    }
}
