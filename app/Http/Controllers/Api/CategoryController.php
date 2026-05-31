<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::select(['id', 'category_name', 'description', 'last_accessed_by', 'updated_at']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('category_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('last_accessed_by', 'like', "%{$search}%");
            });
        }

        $sortCol   = in_array($request->input('sort'), ['category_name', 'description', 'last_accessed_by', 'updated_at']) ? $request->input('sort') : 'category_name';
        $sortDir   = $request->input('dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage   = in_array((int) $request->input('per_page'), [10, 25, 50]) ? (int) $request->input('per_page') : 25;

        $paginated = $query->orderBy($sortCol, $sortDir)->paginate($perPage);

        return response()->json([
            'data'  => $paginated->items(),
            'total' => $paginated->total(),
            'page'  => $paginated->currentPage(),
            'pages' => $paginated->lastPage(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'description'   => 'nullable|string|max:500',
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
            'description'   => 'nullable|string|max:500',
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
        if(!$category){
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json(null, 204);
    }
}
