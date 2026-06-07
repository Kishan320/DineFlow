<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosOrder;
use App\Models\PosKot;
use App\Services\PosService;
use Illuminate\Http\Request;

class PosKotController extends Controller
{
    public function __construct(private PosService $posService)
    {
        $this->middleware('permission:pos.kot.view')->only(['index', 'pending']);
        $this->middleware('permission:pos.kot.create')->only('store');
        $this->middleware('permission:pos.kot.update_status')->only('updateStatus');
    }


    public function store(Request $request, $orderId)
    {
        $request->validate([
            'kot_items'             => 'required|array|min:1',
            'kot_items.*.item_id'   => 'required|integer',
            'kot_items.*.item_name' => 'required|string',
            'kot_items.*.quantity'  => 'required|integer|min:1',
            'notes'                 => 'nullable|string',
        ]);

        $posOrder = PosOrder::forUser(auth()->id())->findOrFail($orderId);

        $kot = $this->posService->generateKot($posOrder, $request->kot_items, $request->notes);
        return response()->json(['success' => true, 'data' => $kot], 201);
    }

    public function index($orderId)
    {
        $posOrder = PosOrder::forUser(auth()->id())->findOrFail($orderId);
        $kots     = $posOrder->kots()->with('items')->orderBy('id')->get();
        return response()->json(['success' => true, 'data' => $kots]);
    }

    public function updateStatus(Request $request, PosKot $posKot)
    {
        $request->validate(['status' => 'required|in:Pending,Preparing,Ready,Served']);

        PosOrder::forUser(auth()->id())->findOrFail($posKot->pos_order_id);

        $posKot->update(['status' => $request->status]);
        return response()->json(['success' => true, 'data' => $posKot->fresh('items')]);
    }

    public function pending()
    {
        $kots = PosKot::with(['items', 'order'])
            ->whereHas('order', fn($q) => $q->where('created_by', auth()->id()))
            ->whereIn('status', ['Pending', 'Preparing'])
            ->orderBy('created_at')
            ->get();
        return response()->json(['success' => true, 'data' => $kots]);
    }
}
