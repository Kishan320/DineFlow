<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::orderBy('category_name')->get();
        return response()->json($category, 200);
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
