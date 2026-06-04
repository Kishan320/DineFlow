<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $search = trim($request->get('search', ''));
        $page = (int) $request->get('page', 1);

        $query = Category::query()
            ->select([
                'id',
                'category_name',
                'description',
                'last_accessed_by',
                'updated_at'
            ]);

        // Search optimization
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('category_name', 'LIKE', "%{$search}%")
                ->orWhere('last_accessed_by', 'LIKE', "%{$search}%");
            });
        }

        // Stable indexed sorting (VERY IMPORTANT)
        $query->orderByDesc('id');

        $categories = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success'      => true,
            'message'      => 'Categories fetched successfully',
            'data'         => $categories->items(),
            'current_page' => $categories->currentPage(),
            'per_page'     => $categories->perPage(),
            'total'        => $categories->total(),
            'last_page'    => $categories->lastPage(),
            'from'         => $categories->firstItem(),
            'to'           => $categories->lastItem(),
        ]);
    }

    public function list()
    {
        $categories = Category::select('id', 'category_name')->orderBy('category_name')->get();
        return response()->json(['success' => true, 'data' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'description' => 'nullable|string|max:500',
        ]);
        $category = new Category();
        $category->category_name = $request->category_name;
        $category->description = $request->description;
        $category->last_accessed_by = 'Administrator';
        $category->save();
        $category->refresh();
        return response()->json($category, 201);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name,' . $category->id,
            'description' => 'nullable|string|max:500',
        ]);
        $category->category_name = $request->category_name;
        $category->description = $request->description;
        $category->last_accessed_by = 'Administrator';
        $category->save();
        $category->refresh();
        return response()->json($category, 200);
    }

    public function destroy(Category $category)
    {
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json(null, 204);
    }
}
