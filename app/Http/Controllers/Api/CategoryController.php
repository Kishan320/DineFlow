<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:categories.view')->only(['index', 'list']);
        $this->middleware('permission:categories.create')->only('store');
        $this->middleware('permission:categories.edit')->only('update');
        $this->middleware('permission:categories.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $userId  = auth()->id();
        $perPage = in_array((int)$request->per_page, [10, 25, 50, 100]) ? (int)$request->per_page : 10;
        $search  = trim($request->get('search', ''));

        $query = Category::withPermissionCheck()
            ->select(['id', 'category_name', 'description', 'last_accessed_by', 'updated_at'])
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('category_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_accessed_by', 'LIKE', "%{$search}%");
            }))
            ->orderByDesc('id');

        $categories = $query->paginate($perPage, ['*'], 'page', (int)$request->get('page', 1));

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
        $categories = Category::withPermissionCheck()
            ->select('id', 'category_name')
            ->orderBy('category_name')
            ->get();
        return response()->json(['success' => true, 'data' => $categories]);
    }

    public function store(Request $request)
    {
        $userId = auth()->id();
        $request->validate([
            'category_name' => [
                'required', 'string', 'max:255',
                \Illuminate\Validation\Rule::unique('categories')->where('created_by', $userId),
            ],
            'description' => 'nullable|string|max:500',
        ]);

        $category = Category::create([
            'created_by'    => $userId,
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'last_accessed_by' => auth()->user()->name,
        ]);

        return response()->json($category->fresh(), 201);
    }

    public function update(Request $request, $id)
    {
        $userId   = auth()->id();
        $category = Category::withPermissionCheck()->findOrFail($id);

        $request->validate([
            'category_name' => [
                'required', 'string', 'max:255',
                \Illuminate\Validation\Rule::unique('categories')->where('created_by', $userId)->ignore($category->id),
            ],
            'description' => 'nullable|string|max:500',
        ]);

        $category->update([
            'category_name'    => $request->category_name,
            'description'      => $request->description,
            'last_accessed_by' => auth()->user()->name,
        ]);

        return response()->json($category->fresh());
    }

    public function destroy($id)
    {
        $category = Category::withPermissionCheck()->findOrFail($id);
        $category->delete();
        return response()->json(null, 204);
    }
}
