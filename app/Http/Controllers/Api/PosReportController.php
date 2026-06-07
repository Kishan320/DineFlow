<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\RestaurantSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:pos.reports.view');
    }

    private function orderBase(): \Illuminate\Database\Eloquent\Builder
    {
        return PosOrder::forUser(auth()->id());
    }

    private function orderItemBase(string $from, string $to): \Illuminate\Database\Eloquent\Builder
    {
        return PosOrderItem::join('pos_orders', 'pos_orders.id', '=', 'pos_order_items.pos_order_id')
            ->where('pos_orders.created_by', auth()->id())
            ->whereBetween('pos_orders.created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ->whereNotIn('pos_orders.status', ['Cancelled']);
    }

    public function dailySales(Request $request)
    {
        $date   = $request->date ?? now()->toDateString();
        $orders = $this->orderBase()->whereDate('created_at', $date)->whereNotIn('status', ['Cancelled'])->get();

        return response()->json(['success' => true, 'data' => [
            'date'           => $date,
            'total_orders'   => $orders->count(),
            'total_sales'    => $orders->sum('net_payable'),
            'total_tax'      => $orders->sum('tax_amount'),
            'total_discount' => $orders->sum('discount'),
            'total_cash'     => $orders->sum('cash_amt'),
            'total_card'     => $orders->sum('card_amt'),
            'total_upi'      => $orders->sum('upi_amt'),
            'total_others'   => $orders->sum('others_amt'),
        ]]);
    }

    public function itemSales(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $items = $this->orderItemBase($from, $to)
            ->select(
                'pos_order_items.item_id',
                'pos_order_items.item_name',
                'pos_order_items.category',
                DB::raw('SUM(pos_order_items.quantity) as total_qty'),
                DB::raw('SUM(pos_order_items.line_total) as total_revenue'),
                DB::raw('SUM(pos_order_items.tax_amount) as total_tax')
            )
            ->groupBy('pos_order_items.item_id', 'pos_order_items.item_name', 'pos_order_items.category')
            ->orderByDesc('total_qty')
            ->paginate(25);

        return response()->json([
            'success'      => true,
            'data'         => $items->items(),
            'total'        => $items->total(),
            'current_page' => $items->currentPage(),
            'last_page'    => $items->lastPage(),
        ]);
    }

    public function categorySales(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $categories = $this->orderItemBase($from, $to)
            ->select(
                'pos_order_items.category',
                DB::raw('SUM(pos_order_items.quantity) as total_qty'),
                DB::raw('SUM(pos_order_items.line_total) as total_revenue')
            )
            ->groupBy('pos_order_items.category')
            ->orderByDesc('total_revenue')
            ->get();

        return response()->json(['success' => true, 'data' => $categories]);
    }

    public function paymentReport(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $data = $this->orderBase()
            ->whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ->whereNotIn('status', ['Cancelled'])
            ->select(
                DB::raw('SUM(cash_amt) as total_cash'),
                DB::raw('SUM(card_amt) as total_card'),
                DB::raw('SUM(upi_amt) as total_upi'),
                DB::raw('SUM(others_amt) as total_others'),
                DB::raw('SUM(net_payable) as total_sales'),
                DB::raw('COUNT(*) as total_orders')
            )->first();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function waiterSales(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $data = $this->orderBase()
            ->whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ->whereNotIn('status', ['Cancelled'])
            ->whereNotNull('waiter_name')
            ->select('waiter_id', 'waiter_name', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(net_payable) as total_sales'))
            ->groupBy('waiter_id', 'waiter_name')
            ->orderByDesc('total_sales')
            ->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function tableSales(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $data = $this->orderBase()
            ->whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ->whereNotIn('status', ['Cancelled'])
            ->whereNotNull('table_label')
            ->select('table_id', 'table_label', DB::raw('COUNT(*) as total_orders'), DB::raw('SUM(net_payable) as total_sales'))
            ->groupBy('table_id', 'table_label')
            ->orderByDesc('total_sales')
            ->get();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function taxReport(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $orders = $this->orderBase()
            ->whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ->whereNotIn('status', ['Cancelled'])
            ->whereNotNull('tax_breakdown')
            ->get(['tax_breakdown', 'tax_amount', 'net_payable']);

        $breakdown = [];
        foreach ($orders as $order) {
            if (is_array($order->tax_breakdown)) {
                foreach ($order->tax_breakdown as $rate => $amount) {
                    $breakdown[$rate] = round(($breakdown[$rate] ?? 0) + $amount, 2);
                }
            }
        }

        return response()->json(['success' => true, 'data' => [
            'breakdown'   => $breakdown,
            'total_tax'   => $orders->sum('tax_amount'),
            'total_sales' => $orders->sum('net_payable'),
        ]]);
    }

    public function billData($orderId)
    {
        $posOrder = PosOrder::forUser(auth()->id())->findOrFail($orderId);
        $posOrder->load(['items', 'kots.items']);
        $settings = RestaurantSettings::forUser(auth()->id())->first();

        return response()->json(['success' => true, 'order' => $posOrder, 'settings' => $settings]);
    }

    public function salesList(Request $request)
    {
        $userId  = auth()->id();
        $perPage = in_array((int)$request->per_page, [10, 25, 50, 100]) ? (int)$request->per_page : 10;

        $base = fn() => $this->orderBase()
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('order_no', 'like', "%{$request->search}%")
                  ->orWhere('invoice_no', 'like', "%{$request->search}%")
                  ->orWhere('customer_name', 'like', "%{$request->search}%");
            }))
            ->when($request->status && $request->status !== 'All', fn($q) => $q->where('status', $request->status))
            ->when($request->order_type && $request->order_type !== 'All', fn($q) => $q->where('order_type', $request->order_type))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('created_at', '<=', $request->date_to));

        $result = $base()->with('items')->orderByDesc('id')->paginate($perPage);

        $orders = collect($result->items())->map(fn($o) => [
            'id'             => $o->id,
            'billNo'         => $o->invoice_no ?? $o->order_no,
            'salesCode'      => $o->order_no,
            'customer'       => $o->customer_name ?? 'Walk-In Guest',
            'customer_phone' => $o->customer_phone,
            'table'          => $o->table_label ?? ($o->order_type === 'Dine In' ? '—' : $o->order_type),
            'waiter'         => $o->waiter_name ?? '—',
            'billType'       => $o->order_type,
            'tax'            => (float) $o->tax_amount,
            'discount'       => (float) $o->discount,
            'subtotal'       => (float) $o->subtotal,
            'total'          => (float) $o->net_payable,
            'paid'           => (float) ($o->cash_amt + $o->card_amt + $o->upi_amt + $o->others_amt),
            'balance'        => max(0, (float)$o->net_payable - (float)($o->cash_amt + $o->card_amt + $o->upi_amt + $o->others_amt)),
            'status'         => $o->status,
            'payment_status' => $o->payment_status,
            'bill_pay_type'  => $o->bill_pay_type,
            'cash_amt'       => (float) $o->cash_amt,
            'card_amt'       => (float) $o->card_amt,
            'card_ref'       => $o->card_ref,
            'upi_amt'        => (float) $o->upi_amt,
            'upi_ref'        => $o->upi_ref,
            'others_amt'     => (float) $o->others_amt,
            'others_type'    => $o->others_type,
            'notes'          => $o->notes,
            'covers'         => $o->covers,
            'date'           => $o->created_at->format('d/m/Y'),
            'time'           => $o->created_at->format('H:i'),
            'created_at'     => $o->created_at->toDateTimeString(),
            'items'          => $o->items->map(fn($i) => [
                'id'       => $i->id,
                'name'     => $i->item_name,
                'category' => $i->category,
                'quantity' => $i->quantity,
                'price'    => (float) $i->unit_price,
                'total'    => (float) $i->line_total,
            ])->toArray(),
        ]);

        $summary = $base()->selectRaw(
            'COUNT(*) as total_bills,
             SUM(net_payable) as total_revenue,
             SUM(cash_amt + card_amt + upi_amt + others_amt) as total_paid,
             SUM(GREATEST(0, net_payable - (cash_amt + card_amt + upi_amt + others_amt))) as total_outstanding'
        )->first();

        return response()->json([
            'success'      => true,
            'data'         => $orders,
            'current_page' => $result->currentPage(),
            'per_page'     => $result->perPage(),
            'total'        => $result->total(),
            'last_page'    => $result->lastPage(),
            'summary'      => [
                'total_bills'       => (int) $summary->total_bills,
                'total_revenue'     => (float) $summary->total_revenue,
                'total_paid'        => (float) $summary->total_paid,
                'total_outstanding' => (float) $summary->total_outstanding,
            ],
        ]);
    }
}
