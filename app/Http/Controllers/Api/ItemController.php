<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $search = trim($request->get('search', ''));
        $page = (int) $request->get('page', 1);

        $query = Item::query()
            ->select(['id', 'code', 'item_name', 'category', 'restaurant_price', 'bar_price', 'room_price', 'state', 'item_type', 'last_accessed_by', 'updated_at']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%")
                  ->orWhere('state', 'LIKE', "%{$search}%")
                  ->orWhere('item_type', 'LIKE', "%{$search}%")
                  ->orWhere('last_accessed_by', 'LIKE', "%{$search}%");
            });
        }

        $query->orderByDesc('id');
        $items = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success'      => true,
            'message'      => 'Items fetched successfully',
            'data'         => $items->items(),
            'current_page' => $items->currentPage(),
            'per_page'     => $items->perPage(),
            'total'        => $items->total(),
            'last_page'    => $items->lastPage(),
            'from'         => $items->firstItem(),
            'to'           => $items->lastItem(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'             => 'required|string|max:50|unique:items,code',
            'item_name'        => 'required|string|max:255',
            'category'         => 'nullable|string|max:255',
            'restaurant_price' => 'required|numeric|min:0',
            'bar_price'        => 'required|numeric|min:0',
            'room_price'       => 'required|numeric|min:0',
            'tax_type'         => 'required|in:Exclusive,Inclusive',
            'tax'              => 'nullable|string|max:100',
            'state'            => 'required|in:On Sale,Off Sale',
            'item_type'        => 'required|in:Physical Item,Digital Item,Service',
            'note'             => 'nullable|string',
            'image'            => 'nullable|image|max:150',
        ]);

        $item = new Item();
        $item->code             = $request->code;
        $item->item_name        = $request->item_name;
        $item->category         = $request->category;
        $item->restaurant_price = $request->restaurant_price;
        $item->bar_price        = $request->bar_price;
        $item->room_price       = $request->room_price;
        $item->tax_type         = $request->tax_type;
        $item->tax              = $request->tax;
        $item->state            = $request->state;
        $item->item_type        = $request->item_type;
        $item->note             = $request->note;
        $item->last_accessed_by = 'Administrator';

        if ($request->hasFile('image')) {
            $item->image_url = '/storage/' . $request->file('image')->store('items', 'public');
        }

        $item->save();
        $item->refresh();
        return response()->json($item, 201);
    }

    public function show(Item $item)
    {
        return response()->json($item);
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'code'             => 'required|string|max:50|unique:items,code,' . $item->id,
            'item_name'        => 'required|string|max:255',
            'category'         => 'nullable|string|max:255',
            'restaurant_price' => 'required|numeric|min:0',
            'bar_price'        => 'required|numeric|min:0',
            'room_price'       => 'required|numeric|min:0',
            'tax_type'         => 'required|in:Exclusive,Inclusive',
            'tax'              => 'nullable|string|max:100',
            'state'            => 'required|in:On Sale,Off Sale',
            'item_type'        => 'required|in:Physical Item,Digital Item,Service',
            'note'             => 'nullable|string',
            'image'            => 'nullable|image|max:150',
        ]);

        $item->code             = $request->code;
        $item->item_name        = $request->item_name;
        $item->category         = $request->category;
        $item->restaurant_price = $request->restaurant_price;
        $item->bar_price        = $request->bar_price;
        $item->room_price       = $request->room_price;
        $item->tax_type         = $request->tax_type;
        $item->tax              = $request->tax;
        $item->state            = $request->state;
        $item->item_type        = $request->item_type;
        $item->note             = $request->note;
        $item->last_accessed_by = 'Administrator';

        if ($request->hasFile('image')) {
            if ($item->image_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $item->image_url));
            }
            $item->image_url = '/storage/' . $request->file('image')->store('items', 'public');
        }

        $item->save();
        $item->refresh();
        return response()->json($item, 200);
    }

    public function destroy(Item $item)
    {
        if ($item->image_url) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $item->image_url));
        }
        $item->delete();
        return response()->json(null, 204);
    }
}
