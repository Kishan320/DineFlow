<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:reports.cashier')->only('cashierReport');
        $this->middleware('permission:reports.daily_sales')->only('dailySales');
        $this->middleware('permission:reports.detailed_sales')->only(['detailedSales', 'salesPrint']);
        $this->middleware('permission:reports.item_wise')->only(['itemWiseSales', 'consolidatedItemWise']);
    }

    private function fmt(float $val): string
    {
        return number_format($val, 2);
    }

    private function dateRange(Request $request): array
    {
        return [
            $request->from_date ?? now()->toDateString(),
            $request->to_date   ?? now()->toDateString(),
        ];
    }

    private function orderBase(string $from, string $to): \Illuminate\Database\Eloquent\Builder
    {
        return PosOrder::forUser(auth()->id())
            ->whereBetween('created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ->whereNotIn('status', ['Cancelled']);
    }

    private function orderItemBase(string $from, string $to): \Illuminate\Database\Eloquent\Builder
    {
        return PosOrderItem::join('pos_orders', 'pos_orders.id', '=', 'pos_order_items.pos_order_id')
            ->where('pos_orders.created_by', auth()->id())
            ->whereBetween('pos_orders.created_at', ["{$from} 00:00:00", "{$to} 23:59:59"])
            ->whereNotIn('pos_orders.status', ['Cancelled']);
    }

    public function cashierReport(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $orders      = $this->orderBase($from, $to)->get();

        $totalBills      = $orders->count();
        $totalSales      = $orders->sum('net_payable');
        $totalCash       = $orders->sum('cash_amt');
        $totalCard       = $orders->sum('card_amt');
        $totalUpi        = $orders->sum('upi_amt');
        $totalOthers     = $orders->sum('others_amt');
        $totalTax        = $orders->sum('tax_amount');
        $totalDiscount   = $orders->sum('discount');
        $totalCollection = $totalCash + $totalCard + $totalUpi + $totalOthers;

        $dineIn   = $orders->where('order_type', 'Dine In');
        $takeaway = $orders->where('order_type', 'Takeaway');
        $delivery = $orders->where('order_type', 'Delivery');

        $rows = [
            ['label' => 'Cash Bill',       'amount' => $this->fmt($totalSales),      'bills' => $totalBills],
            ['label' => 'Credit Bill',      'amount' => $this->fmt(0),                'bills' => 0],
            ['label' => 'Guest Bill',       'amount' => $this->fmt(0),                'bills' => 0],
            ['label' => 'Total Sales',      'amount' => $this->fmt($totalSales),      'bills' => $totalBills, 'total' => true],
            ['label' => 'Cash',             'amount' => $this->fmt($totalCash),        'bills' => $orders->where('cash_amt', '>', 0)->count()],
            ['label' => 'Card',             'amount' => $this->fmt($totalCard),        'bills' => $orders->where('card_amt', '>', 0)->count()],
            ['label' => 'UPI',              'amount' => $this->fmt($totalUpi),         'bills' => $orders->where('upi_amt', '>', 0)->count()],
            ['label' => 'Others',           'amount' => $this->fmt($totalOthers),      'bills' => $orders->where('others_amt', '>', 0)->count()],
            ['label' => 'Total Collection', 'amount' => $this->fmt($totalCollection),  'bills' => $orders->whereIn('payment_status', ['Paid', 'Partial'])->count(), 'total' => true],
            ['label' => 'Dine In',          'amount' => $this->fmt($dineIn->sum('net_payable')),   'bills' => $dineIn->count()],
            ['label' => 'Take Away',        'amount' => $this->fmt($takeaway->sum('net_payable')), 'bills' => $takeaway->count()],
            ['label' => 'Delivery',         'amount' => $this->fmt($delivery->sum('net_payable')), 'bills' => $delivery->count()],
            ['label' => 'Total',            'amount' => $this->fmt($totalSales),      'bills' => $totalBills, 'total' => true],
            ['label' => 'Tax Details',      'amount' => '',                            'bills' => '', 'total' => true],
            ['label' => 'Total Tax Amount', 'amount' => $this->fmt($totalTax),         'bills' => '', 'total' => true],
            ['label' => 'Total Discount',   'amount' => $this->fmt($totalDiscount),    'bills' => ''],
        ];

        return response()->json(['success' => true, 'data' => $rows, 'from' => $from, 'to' => $to]);
    }

    public function dailySales(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $orders      = $this->orderBase($from, $to)->orderBy('id')->get();

        $rows = $orders->map(fn($o, $i) => [
            'no'         => $i + 1,
            'billNo'     => $o->invoice_no ?? $o->order_no,
            'salesCode'  => $o->order_no,
            'date'       => $o->created_at->format('d-m-y'),
            'customer'   => $o->customer_name ?? 'Walk-In Customer',
            'billType'   => 'Cash Bill',
            'billAmount' => $this->fmt($o->net_payable),
            'taxAmount'  => $this->fmt($o->tax_amount),
            'cash'       => $o->cash_amt > 0   ? $this->fmt($o->cash_amt)   : '',
            'card'       => $o->card_amt > 0   ? $this->fmt($o->card_amt)   : '',
            'upi'        => $o->upi_amt > 0    ? $this->fmt($o->upi_amt)    : '',
            'others'     => $o->others_amt > 0 ? $this->fmt($o->others_amt) : '',
        ]);

        $totals = [
            'billAmount' => $this->fmt($orders->sum('net_payable')),
            'taxAmount'  => $this->fmt($orders->sum('tax_amount')),
            'cash'       => $this->fmt($orders->sum('cash_amt')),
            'card'       => $this->fmt($orders->sum('card_amt')),
            'upi'        => $this->fmt($orders->sum('upi_amt')),
            'others'     => $this->fmt($orders->sum('others_amt')),
        ];

        return response()->json(['success' => true, 'data' => $rows, 'totals' => $totals, 'from' => $from, 'to' => $to]);
    }

    public function detailedSales(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $orders      = $this->orderBase($from, $to)->with('items')->orderBy('id')->get();

        $bills = $orders->map(fn($o, $i) => [
            'no'        => $i + 1,
            'salesCode' => $o->order_no,
            'billNo'    => $o->invoice_no ?? $o->order_no,
            'date'      => $o->created_at->format('d-m-y'),
            'customer'  => $o->customer_name ?? 'Walk-In Customer',
            'billType'  => 'Cash Bill',
            'amount'    => $this->fmt($o->net_payable),
            'items'     => $o->items->map(fn($item) => [
                'desc'   => $item->item_name,
                'price'  => $this->fmt($item->unit_price),
                'qty'    => $item->quantity,
                'value'  => $this->fmt($item->unit_price * $item->quantity),
                'tax'    => $this->fmt($item->tax_amount),
                'amount' => $this->fmt($item->line_total + $item->tax_amount),
            ])->toArray(),
        ]);

        $completed  = $orders->whereIn('status', ['Completed', 'Served']);
        $partial    = $orders->whereNotIn('status', ['Completed', 'Served', 'Cancelled']);
        $dineIn     = $orders->where('order_type', 'Dine In');
        $takeaway   = $orders->where('order_type', 'Takeaway');
        $delivery   = $orders->where('order_type', 'Delivery');
        $totalSales = $orders->sum('net_payable');

        $waiterSales = $orders->groupBy('waiter_name')->map(fn($g, $name) => [
            'label'  => $name ?: 'Unassigned',
            'amount' => 'Rs. ' . $this->fmt($g->sum('net_payable')),
            'count'  => $g->count(),
        ])->values()->toArray();

        $analysis = [
            'order' => [
                ['label' => 'Completed Orders',      'amount' => 'Rs. ' . $this->fmt($completed->sum('net_payable')), 'count' => $completed->count()],
                ['label' => 'Partially Paid Orders', 'amount' => 'Rs. ' . $this->fmt($partial->sum('net_payable')),   'count' => $partial->count()],
                ['label' => 'Total',                 'amount' => 'Rs. ' . $this->fmt($totalSales), 'count' => $orders->count(), 'total' => true],
            ],
            'bill_type' => [
                ['label' => 'Cash Bill',   'amount' => 'Rs. ' . $this->fmt($totalSales), 'count' => $orders->count()],
                ['label' => 'Credit Bill', 'amount' => 'Rs. 0.00', 'count' => 0],
                ['label' => 'Guest Bill',  'amount' => 'Rs. 0.00', 'count' => 0],
                ['label' => 'Total',       'amount' => 'Rs. ' . $this->fmt($totalSales), 'count' => $orders->count(), 'total' => true],
            ],
            'pay_mode' => [
                ['label' => 'Cash',   'amount' => 'Rs. ' . $this->fmt($orders->sum('cash_amt')),   'count' => $orders->where('cash_amt', '>', 0)->count()],
                ['label' => 'Card',   'amount' => 'Rs. ' . $this->fmt($orders->sum('card_amt')),   'count' => $orders->where('card_amt', '>', 0)->count()],
                ['label' => 'UPI',    'amount' => 'Rs. ' . $this->fmt($orders->sum('upi_amt')),    'count' => $orders->where('upi_amt', '>', 0)->count()],
                ['label' => 'Others', 'amount' => 'Rs. ' . $this->fmt($orders->sum('others_amt')), 'count' => $orders->where('others_amt', '>', 0)->count()],
                ['label' => 'Total',  'amount' => 'Rs. ' . $this->fmt($orders->sum('cash_amt') + $orders->sum('card_amt') + $orders->sum('upi_amt') + $orders->sum('others_amt')), 'count' => $orders->count(), 'total' => true],
            ],
            'order_type' => [
                ['label' => 'Dine In',   'amount' => 'Rs. ' . $this->fmt($dineIn->sum('net_payable')),   'count' => $dineIn->count()],
                ['label' => 'Take Away', 'amount' => 'Rs. ' . $this->fmt($takeaway->sum('net_payable')), 'count' => $takeaway->count()],
                ['label' => 'Delivery',  'amount' => 'Rs. ' . $this->fmt($delivery->sum('net_payable')), 'count' => $delivery->count()],
            ],
            'staff' => $waiterSales ?: [['label' => 'No staff data', 'amount' => 'Rs. 0.00', 'count' => 0]],
        ];

        return response()->json([
            'success'    => true,
            'bills'      => $bills,
            'analysis'   => $analysis,
            'grandTotal' => $this->fmt($totalSales),
            'from'       => $from,
            'to'         => $to,
        ]);
    }

    public function salesPrint(Request $request)
    {
        return $this->detailedSales($request);
    }

    public function itemWiseSales(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $itemNames = $this->orderItemBase($from, $to)
            ->distinct()
            ->orderBy('pos_order_items.item_name')
            ->pluck('pos_order_items.item_name')
            ->toArray();

        $query = $this->orderItemBase($from, $to)
            ->when($request->item_name, fn($q) => $q->where('pos_order_items.item_name', $request->item_name))
            ->orderBy('pos_orders.id')
            ->select(
                'pos_orders.invoice_no',
                'pos_orders.order_no',
                'pos_orders.created_at',
                'pos_order_items.item_name',
                'pos_order_items.quantity',
                'pos_order_items.unit_price',
                'pos_order_items.line_total',
                'pos_order_items.tax_amount',
            )
            ->get();

        $rows = $query->map(fn($r, $i) => [
            'no'          => $i + 1,
            'billNo'      => $r->invoice_no ?? $r->order_no,
            'salesCode'   => $r->order_no,
            'date'        => \Carbon\Carbon::parse($r->created_at)->format('d-m-y'),
            'item'        => $r->item_name,
            'qty'         => $r->quantity,
            'salesValue'  => $this->fmt($r->line_total),
            'taxAmount'   => $this->fmt($r->tax_amount),
            'salesAmount' => $this->fmt($r->line_total + $r->tax_amount),
        ]);

        $totals = [
            'qty'         => $query->sum('quantity'),
            'salesValue'  => $this->fmt($query->sum('line_total')),
            'taxAmount'   => $this->fmt($query->sum('tax_amount')),
            'salesAmount' => $this->fmt($query->sum(fn($r) => $r->line_total + $r->tax_amount)),
        ];

        return response()->json(['success' => true, 'data' => $rows, 'totals' => $totals, 'itemNames' => $itemNames, 'from' => $from, 'to' => $to]);
    }

    public function consolidatedItemWise(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $rows = $this->orderItemBase($from, $to)
            ->select(
                DB::raw('DATE(pos_orders.created_at) as sale_date'),
                'pos_order_items.item_name as desc',
                'pos_order_items.unit_price as price',
                DB::raw('SUM(pos_order_items.quantity) as qty'),
                DB::raw('SUM(pos_order_items.line_total) as value'),
                DB::raw('SUM(pos_order_items.tax_amount) as tax'),
                DB::raw('SUM(pos_order_items.line_total + pos_order_items.tax_amount) as amount')
            )
            ->groupBy('sale_date', 'pos_order_items.item_name', 'pos_order_items.unit_price')
            ->orderBy('sale_date')
            ->orderBy('pos_order_items.item_name')
            ->get()
            ->map(fn($r, $i) => [
                'no'     => $i + 1,
                'date'   => \Carbon\Carbon::parse($r->sale_date)->format('d-m-y'),
                'desc'   => $r->desc,
                'price'  => $this->fmt($r->price),
                'qty'    => $r->qty,
                'value'  => $this->fmt($r->value),
                'tax'    => $this->fmt($r->tax),
                'amount' => $this->fmt($r->amount),
            ]);

        $grandTotal = $this->fmt(
            $this->orderItemBase($from, $to)
                ->sum(DB::raw('pos_order_items.line_total + pos_order_items.tax_amount'))
        );

        return response()->json(['success' => true, 'data' => $rows, 'grandTotal' => $grandTotal, 'from' => $from, 'to' => $to]);
    }
}
