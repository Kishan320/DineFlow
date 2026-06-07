<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use App\Services\PosService;
use Illuminate\Http\Request;

class PosProductController extends Controller
{
    public function __construct(private PosService $posService)
    {
        $this->middleware('permission:pos.access')->only(['index', 'categories']);
    }

    public function index(Request $request)
    {
        $userId   = auth()->id();
        $perPage  = in_array((int)$request->per_page, [12, 24, 48, 96]) ? (int)$request->per_page : 24;
        $search   = trim($request->search ?? '');
        $category = trim($request->category ?? '');

        $query = Item::forUser($userId)
            ->select(['id', 'code', 'item_name', 'category', 'restaurant_price', 'tax_type', 'tax', 'state', 'image_url', 'note'])
            ->where('state', 'On Sale')
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            }))
            ->when($category, fn($q) => $q->where('category', $category))
            ->orderBy('item_name');

        $result = $query->paginate($perPage);
        $items  = collect($result->items())->map(fn($item) => $this->formatItem($item));

        return response()->json([
            'success'      => true,
            'data'         => $items,
            'current_page' => $result->currentPage(),
            'per_page'     => $result->perPage(),
            'total'        => $result->total(),
            'last_page'    => $result->lastPage(),
        ]);
    }

    public function categories()
    {
        $categories = Category::forUser(auth()->id())
            ->orderBy('category_name')
            ->select(['id', 'category_name'])
            ->get()
            ->map(fn($c) => ['id' => $c->id, 'label' => $c->category_name]);

        return response()->json(['success' => true, 'data' => $categories]);
    }

    private function formatItem(Item $item): array
    {
        $taxPercent = 0;
        if ($item->tax && is_numeric($item->tax)) {
            $taxPercent = (float) $item->tax;
        } elseif ($item->tax) {
            $tax = \App\Models\Tax::forUser(auth()->id())
                ->where(fn($q) => $q->where('description', $item->tax)->orWhere('hsn_code', $item->tax))
                ->first();
            if ($tax) {
                $taxPercent = (float) $tax->tax_percent ?: (float)($tax->cgst + $tax->sgst + $tax->igst + $tax->vat);
            }
        }

        return [
            'id'          => $item->id,
            'code'        => $item->code,
            'name'        => $item->item_name,
            'category'    => $item->category,
            'price'       => (float) $item->restaurant_price,
            'tax_percent' => $taxPercent,
            'tax_type'    => $item->tax_type,
            'available'   => $item->state === 'On Sale',
            'image'       => $item->image_url
                ? (str_starts_with($item->image_url, '/') ? $item->image_url : '/storage/' . $item->image_url)
                : '/images/menu/placeholder.jpg',
            'note'        => $item->note,
        ];
    }
}
