<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // Allowed per page limits
        $perPage = (int) $request->get('per_page', 10);

        if (!in_array($perPage, [10, 25, 100])) {
            $perPage = 10;
        }

        $search = trim($request->get('search', ''));

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

        // Standard pagination (matches frontend store which expects total/pages)
        $categories = $query
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Categories fetched successfully',
            'data' => $categories->items(),
            'total' => $categories->total(),
            'pages' => $categories->lastPage(),
            'per_page' => $categories->perPage(),
            'current_page' => $categories->currentPage(),
            'next_page_url' => $categories->nextPageUrl(),
            'prev_page_url' => $categories->previousPageUrl(),
        ]);
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
