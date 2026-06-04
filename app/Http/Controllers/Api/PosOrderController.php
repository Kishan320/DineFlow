<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosOrder;
use App\Services\PosService;
use Illuminate\Http\Request;

class PosOrderController extends Controller
{
    public function __construct(private PosService $posService) {}

    public function index(Request $request)
    {
        $perPage = in_array((int)$request->per_page, [10, 25, 50, 100]) ? (int)$request->per_page : 25;
        $query   = PosOrder::with(['items'])
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('order_no', 'like', "%{$request->search}%")
                  ->orWhere('invoice_no', 'like', "%{$request->search}%")
                  ->orWhere('customer_name', 'like', "%{$request->search}%");
            }))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->order_type, fn($q) => $q->where('order_type', $request->order_type))
            ->when($request->session_id, fn($q) => $q->where('session_id', $request->session_id))
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->orderByDesc('id');

        $result = $query->paginate($perPage);

        return response()->json([
            'success'      => true,
            'data'         => $result->items(),
            'current_page' => $result->currentPage(),
            'per_page'     => $result->perPage(),
            'total'        => $result->total(),
            'last_page'    => $result->lastPage(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cart_items'              => 'required|array|min:1',
            'cart_items.*.item_id'    => 'required|integer|exists:items,id',
            'cart_items.*.quantity'   => 'required|integer|min:1',
            'order_type'              => 'required|in:Dine In,Takeaway,Delivery',
            'customer_id'             => 'nullable|integer|exists:customers,id',
            'table_id'                => 'nullable|integer|exists:restaurant_tables,id',
            'waiter_id'               => 'nullable|integer|exists:waiters,id',
            'covers'                  => 'nullable|integer|min:1',
            'discount'                => 'nullable|numeric|min:0',
            'discount_type'           => 'nullable|in:flat,percent',
            'bill_pay_type'           => 'nullable|string',
            'cash_amt'                => 'nullable|numeric|min:0',
            'card_amt'                => 'nullable|numeric|min:0',
            'card_ref'                => 'nullable|string|max:100',
            'others_amt'              => 'nullable|numeric|min:0',
            'others_type'             => 'nullable|string|max:100',
            'others_ref'              => 'nullable|string|max:100',
            'upi_amt'                 => 'nullable|numeric|min:0',
            'upi_ref'                 => 'nullable|string|max:100',
            'delivery_address'        => 'nullable|string',
            'delivery_charge'         => 'nullable|numeric|min:0',
            'delivery_notes'          => 'nullable|string',
            'notes'                   => 'nullable|string',
            'payment_note'            => 'nullable|string',
        ]);

        $data = $request->all();
        $data['last_accessed_by'] = $request->user()?->name ?? 'Administrator';

        if ($request->customer_id) {
            $customer = \App\Models\Customer::find($request->customer_id);
            $data['customer_name']  = $customer?->company_name ?? 'Walk-In Guest';
            $data['customer_phone'] = $customer?->mobile ?? null;
        }

        if ($request->table_id) {
            $table = \App\Models\RestaurantTable::find($request->table_id);
            $data['table_label'] = $table?->table_name ?? null;
        }

        if ($request->waiter_id) {
            $waiter = \App\Models\Waiter::find($request->waiter_id);
            $data['waiter_name'] = $waiter?->name ?? null;
        }

        $order = $this->posService->createOrder($data);

        return response()->json(['success' => true, 'data' => $order], 201);
    }

    public function show(PosOrder $posOrder)
    {
        $posOrder->load(['items', 'kots.items', 'statusHistories']);
        return response()->json(['success' => true, 'data' => $posOrder]);
    }

    public function updateStatus(Request $request, PosOrder $posOrder)
    {
        $request->validate([
            'status' => 'required|in:Pending,Confirmed,Preparing,Ready,Served,Completed,Cancelled',
        ]);

        $order = $this->posService->updateOrderStatus(
            $posOrder,
            $request->status,
            $request->user()?->name ?? 'Administrator'
        );

        return response()->json(['success' => true, 'data' => $order]);
    }

    public function ongoing()
    {
        $orders = PosOrder::with(['items'])
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->orderByDesc('id')
            ->get();

        return response()->json(['success' => true, 'data' => $orders]);
    }

    public function destroy(PosOrder $posOrder)
    {
        if (!in_array($posOrder->status, ['Pending', 'Cancelled'])) {
            return response()->json(['success' => false, 'message' => 'Only pending or cancelled orders can be deleted'], 422);
        }
        $posOrder->delete();
        return response()->json(null, 204);
    }
}
