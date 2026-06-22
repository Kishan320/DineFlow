<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Helper to get allowed user IDs for the restaurant
        $user = User::find($userId);
        $userIds = [$userId];
        if ($user && $user->restaurant_id) {
            $userIds = User::where('restaurant_id', $user->restaurant_id)->pluck('id')->toArray();
        }

        // 1. KPI Data
        // Today's Orders
        $ordersTodayCount = PosOrder::forUser($userId)
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->count();

        $ordersYesterdayCount = PosOrder::forUser($userId)
            ->whereDate('created_at', $yesterday)
            ->where('status', '!=', 'cancelled')
            ->count();

        $ordersChange = $this->calculateChange($ordersTodayCount, $ordersYesterdayCount);

        // Today's Revenue
        $revenueToday = PosOrder::forUser($userId)
            ->whereDate('created_at', $today)
            ->where('payment_status', 'Paid')
            ->sum('net_payable');

        $revenueYesterday = PosOrder::forUser($userId)
            ->whereDate('created_at', $yesterday)
            ->where('payment_status', 'Paid')
            ->sum('net_payable');

        $revenueChange = $this->calculateChange($revenueToday, $revenueYesterday);

        // Active Tables
        $totalTables = RestaurantTable::forUser($userId)->count();
        $activeTables = RestaurantTable::forUser($userId)->where('status', 'Occupied')->count();

        // Customers Today (unique customers + walk-ins counted as 1 each if they don't have customer_id)
        $customersToday = PosOrder::forUser($userId)
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->distinct('customer_id')
            ->count('customer_id');
        // Add walkins
        $walkinsToday = PosOrder::forUser($userId)
            ->whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->whereNull('customer_id')
            ->count();
        $totalCustomersToday = $customersToday + $walkinsToday;

        $customersYesterday = PosOrder::forUser($userId)
            ->whereDate('created_at', $yesterday)
            ->where('status', '!=', 'cancelled')
            ->distinct('customer_id')
            ->count('customer_id');
        $walkinsYesterday = PosOrder::forUser($userId)
            ->whereDate('created_at', $yesterday)
            ->where('status', '!=', 'cancelled')
            ->whereNull('customer_id')
            ->count();
        $totalCustomersYesterday = $customersYesterday + $walkinsYesterday;
        $customersChange = $totalCustomersToday - $totalCustomersYesterday;
        $customersChangeText = $customersChange > 0 ? "+$customersChange" : (string)$customersChange;

        // Average Order Value
        $aovToday = $ordersTodayCount > 0 ? $revenueToday / $ordersTodayCount : 0;
        $aovYesterday = $ordersYesterdayCount > 0 ? $revenueYesterday / $ordersYesterdayCount : 0;
        $aovChange = $this->calculateChange($aovToday, $aovYesterday);

        // Pending Bills
        $pendingBillsCount = PosOrder::forUser($userId)
            ->whereDate('created_at', $today)
            ->where('status', 'pending')
            ->count();

        $pendingBillsAmount = PosOrder::forUser($userId)
            ->whereDate('created_at', $today)
            ->where('status', 'pending')
            ->sum('net_payable');

        $kpis = [
            [
                'id' => 'kpi-orders',
                'label' => "Today's Orders",
                'value' => (string) $ordersTodayCount,
                'change' => $ordersChange['text'],
                'changeType' => $ordersChange['type'],
                'subtext' => "vs yesterday $ordersYesterdayCount",
                'icon' => 'ShoppingBag',
                'color' => 'text-primary',
                'bg' => 'bg-accent',
                'hero' => true
            ],
            [
                'id' => 'kpi-revenue',
                'label' => "Today's Revenue",
                'value' => '$' . number_format($revenueToday, 2),
                'change' => $revenueChange['text'],
                'changeType' => $revenueChange['type'],
                'subtext' => "vs yesterday $" . number_format($revenueYesterday, 2),
                'icon' => 'DollarSign',
                'color' => 'text-secondary',
                'bg' => 'bg-secondary/10',
                'hero' => true
            ],
            [
                'id' => 'kpi-tables',
                'label' => 'Active Tables',
                'value' => "$activeTables / $totalTables",
                'change' => $totalTables > 0 ? round(($activeTables / $totalTables) * 100) . '%' : '0%',
                'changeType' => 'neutral',
                'subtext' => "$activeTables occupied, " . ($totalTables - $activeTables) . " available",
                'icon' => 'Grid3X3',
                'color' => 'text-info',
                'bg' => 'bg-info/10'
            ],
            [
                'id' => 'kpi-customers',
                'label' => 'Customers Today',
                'value' => (string) $totalCustomersToday,
                'change' => $customersChangeText,
                'changeType' => $customersChange > 0 ? 'positive' : ($customersChange < 0 ? 'negative' : 'neutral'),
                'subtext' => 'vs yesterday',
                'icon' => 'Users',
                'color' => 'text-secondary',
                'bg' => 'bg-secondary/10'
            ],
            [
                'id' => 'kpi-aov',
                'label' => 'Avg Order Value',
                'value' => '$' . number_format($aovToday, 2),
                'change' => $aovChange['text'],
                'changeType' => $aovChange['type'],
                'subtext' => 'vs $' . number_format($aovYesterday, 2) . ' yesterday',
                'icon' => 'TrendingUp',
                'color' => 'text-warning',
                'bg' => 'bg-warning/10'
            ],
            [
                'id' => 'kpi-pending',
                'label' => 'Pending Bills',
                'value' => (string) $pendingBillsCount,
                'change' => $pendingBillsCount > 0 ? 'Action needed' : 'All clear',
                'changeType' => $pendingBillsCount > 0 ? 'alert' : 'positive',
                'subtext' => '$' . number_format($pendingBillsAmount, 2) . ' outstanding',
                'icon' => 'Clock',
                'color' => 'text-danger',
                'bg' => 'bg-danger/10'
            ],
        ];

        // 2. Charts Data
        // Hourly Sales Data
        $hourlySalesRaw = PosOrder::forUser($userId)
            ->whereDate('created_at', $today)
            ->where('payment_status', 'Paid')
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(net_payable) as revenue')
            )
            ->groupBy('hour')
            ->get()
            ->keyBy('hour');

        $hourlySales = [];
        for ($i = 9; $i <= 20; $i++) {
            $hourLabel = $i > 12 ? ($i - 12) . 'PM' : ($i == 12 ? '12PM' : $i . 'AM');
            $data = $hourlySalesRaw->get($i);
            $hourlySales[] = [
                'hour' => $hourLabel,
                'orders' => $data ? $data->orders : 0,
                'revenue' => $data ? (float) $data->revenue : 0,
            ];
        }

        // Weekly Sales Data
        $startOfWeek = Carbon::now()->startOfWeek();
        $weeklySalesRaw = PosOrder::forUser($userId)
            ->whereBetween('created_at', [$startOfWeek, Carbon::now()->endOfWeek()])
            ->where('status', 'paid')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(net_payable) as revenue')
            )
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $weeklySales = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dateString = $date->format('Y-m-d');
            $dayLabel = $date->format('D');
            $data = $weeklySalesRaw->get($dateString);
            $weeklySales[] = [
                'day' => $dayLabel,
                'orders' => $data ? $data->orders : 0,
                'revenue' => $data ? (float) $data->revenue : 0,
            ];
        }

        // 3. Top Selling Items
        $topSellingItemsRaw = DB::table('pos_order_items')
            ->join('pos_orders', 'pos_order_items.pos_order_id', '=', 'pos_orders.id')
            ->whereIn('pos_orders.created_by', $userIds)
            ->whereDate('pos_orders.created_at', $today)
            ->select(
                'pos_order_items.item_name as name',
                'pos_order_items.category',
                DB::raw('SUM(pos_order_items.quantity) as quantity'),
                DB::raw('SUM(pos_order_items.line_total) as revenue')
            )
            ->groupBy('pos_order_items.item_id', 'pos_order_items.item_name', 'pos_order_items.category')
            ->orderBy('quantity', 'desc')
            ->limit(5)
            ->get();

        $topSellingItems = $topSellingItemsRaw->map(function ($item, $index) {
            return [
                'id' => 'tsi-' . ($index + 1),
                'name' => $item->name,
                'category' => $item->category,
                'quantity' => (int) $item->quantity,
                'revenue' => (float) $item->revenue,
            ];
        });

        // 4. Recent Orders
        $recentOrdersRaw = PosOrder::forUser($userId)
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentOrders = $recentOrdersRaw->map(function ($order) {
            return [
                'id' => $order->id,
                'billNo' => $order->order_no ?? $order->invoice_no,
                'customer' => $order->customer_name ?: 'Walk-In Guest',
                'table' => $order->table_label ?: 'Takeaway',
                'billType' => $order->order_type,
                'total' => (float) $order->net_payable,
                'status' => ucfirst($order->status),
                'time' => $order->created_at->format('H:i'),
            ];
        });

        return response()->json([
            'kpis' => $kpis,
            'hourlySales' => $hourlySales,
            'weeklySales' => $weeklySales,
            'topSellingItems' => $topSellingItems,
            'recentOrders' => $recentOrders,
        ]);
    }

    private function calculateChange($today, $yesterday)
    {
        if ($yesterday == 0) {
            if ($today > 0) return ['text' => '+100%', 'type' => 'positive'];
            return ['text' => '0%', 'type' => 'neutral'];
        }

        $change = (($today - $yesterday) / $yesterday) * 100;
        $formattedChange = number_format(abs($change), 1) . '%';

        if ($change > 0) {
            return ['text' => '+' . $formattedChange, 'type' => 'positive'];
        } elseif ($change < 0) {
            return ['text' => '-' . $formattedChange, 'type' => 'negative'];
        }

        return ['text' => '0%', 'type' => 'neutral'];
    }
}
