<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index()
    {
        return response()->json(Item::orderBy('item_name')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'              => 'required|string|max:50|unique:items,code',
            'item_name'         => 'required|string|max:255',
            'category'          => 'nullable|string|max:255',
            'restaurant_price'  => 'required|numeric|min:0',
            'bar_price'         => 'required|numeric|min:0',
            'room_price'        => 'required|numeric|min:0',
            'tax_type'          => 'required|in:Exclusive,Inclusive',
            'tax'               => 'nullable|string|max:100',
            'state'             => 'required|in:On Sale,Off Sale',
            'item_type'         => 'required|in:Physical Item,Digital Item,Service',
            'note'              => 'nullable|string',
            'image'             => 'nullable|image|max:150',
        ]);

        if ($request->hasFile('image')) {
            $data['image_url'] = '/storage/' . $request->file('image')->store('items', 'public');
        }

        $data['last_accessed_by'] = 'Administrator';
        unset($data['image']);
        return response()->json(Item::create($data), 201);
    }

    public function show(Item $item)
    {
        return response()->json($item);
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'code'              => 'required|string|max:50|unique:items,code,' . $item->id,
            'item_name'         => 'required|string|max:255',
            'category'          => 'nullable|string|max:255',
            'restaurant_price'  => 'required|numeric|min:0',
            'bar_price'         => 'required|numeric|min:0',
            'room_price'        => 'required|numeric|min:0',
            'tax_type'          => 'required|in:Exclusive,Inclusive',
            'tax'               => 'nullable|string|max:100',
            'state'             => 'required|in:On Sale,Off Sale',
            'item_type'         => 'required|in:Physical Item,Digital Item,Service',
            'note'              => 'nullable|string',
            'image'             => 'nullable|image|max:150',
        ]);

        if ($request->hasFile('image')) {
            if ($item->image_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $item->image_url));
            }
            $data['image_url'] = '/storage/' . $request->file('image')->store('items', 'public');
        }

        $data['last_accessed_by'] = 'Administrator';
        unset($data['image']);
        $item->update($data);
        return response()->json($item);
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
