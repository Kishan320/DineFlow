<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:items.view')->only(['index', 'show']);
        $this->middleware('permission:items.create')->only('store');
        $this->middleware('permission:items.edit')->only('update');
        $this->middleware('permission:items.delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $userId = auth()->id();
        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;
        $search = trim($request->get('search', ''));

        $query = Item::withPermissionCheck()
            ->select(['id', 'code', 'item_name', 'category', 'restaurant_price', 'bar_price', 'room_price', 'state', 'item_type', 'last_accessed_by', 'updated_at'])
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('item_name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('category', 'LIKE', "%{$search}%")
                    ->orWhere('state', 'LIKE', "%{$search}%")
                    ->orWhere('item_type', 'LIKE', "%{$search}%");
            }))
            ->orderByDesc('id');

        $items = $query->paginate($perPage, ['*'], 'page', (int) $request->get('page', 1));

        return response()->json([
            'success' => true,
            'message' => 'Items fetched successfully',
            'data' => $items->items(),
            'current_page' => $items->currentPage(),
            'per_page' => $items->perPage(),
            'total' => $items->total(),
            'last_page' => $items->lastPage(),
            'from' => $items->firstItem(),
            'to' => $items->lastItem(),
        ]);
    }

    public function store(Request $request)
    {
        $userId = auth()->id();
        $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('items')->where('created_by', $userId)],
            'item_name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'restaurant_price' => 'required|numeric|min:0',
            'bar_price' => 'required|numeric|min:0',
            'room_price' => 'required|numeric|min:0',
            'tax_type' => 'required|in:Exclusive,Inclusive',
            'tax' => 'nullable|string|max:100',
            'state' => 'required|in:On Sale,Off Sale',
            'item_type' => 'required|in:Physical Item,Digital Item,Service',
            'note' => 'nullable|string',
            'image_url' => 'nullable|string',
        ]);

        $data = [
            'created_by' => $userId,
            'code' => $request->code,
            'item_name' => $request->item_name,
            'category' => $request->category,
            'restaurant_price' => $request->restaurant_price,
            'bar_price' => $request->bar_price,
            'room_price' => $request->room_price,
            'tax_type' => $request->tax_type,
            'tax' => $request->tax,
            'state' => $request->state,
            'item_type' => $request->item_type,
            'note' => $request->note,
            'image_url' => $request->image_url,
            'last_accessed_by' => auth()->user()->name,
        ];

        $item = Item::create($data);

        return response()->json($item->fresh(), 201);
    }

    public function show($id)
    {
        $item = Item::withPermissionCheck()->findOrFail($id);

        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $userId = auth()->id();
        $item = Item::withPermissionCheck()->findOrFail($id);

        $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('items')->where('created_by', $userId)->ignore($item->id)],
            'item_name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'restaurant_price' => 'required|numeric|min:0',
            'bar_price' => 'required|numeric|min:0',
            'room_price' => 'required|numeric|min:0',
            'tax_type' => 'required|in:Exclusive,Inclusive',
            'tax' => 'nullable|string|max:100',
            'state' => 'required|in:On Sale,Off Sale',
            'item_type' => 'required|in:Physical Item,Digital Item,Service',
            'note' => 'nullable|string',
            'image_url' => 'nullable|string',
        ]);

        $data = [
            'code' => $request->code,
            'item_name' => $request->item_name,
            'category' => $request->category,
            'restaurant_price' => $request->restaurant_price,
            'bar_price' => $request->bar_price,
            'room_price' => $request->room_price,
            'tax_type' => $request->tax_type,
            'tax' => $request->tax,
            'state' => $request->state,
            'item_type' => $request->item_type,
            'note' => $request->note,
            'image_url' => $request->image_url,
            'last_accessed_by' => auth()->user()->name,
        ];

        $item->update($data);

        return response()->json($item->fresh());
    }

    public function destroy($id)
    {
        $item = Item::withPermissionCheck()->findOrFail($id);
        $item->delete();

        return response()->json(null, 204);
    }
}
