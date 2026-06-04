<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use App\Models\TableSession;
use App\Models\Waiter;
use Illuminate\Http\Request;

class PosTableController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $tables = RestaurantTable::forUser($userId)->orderBy('table_name')->get();

        $openSessions = TableSession::whereHas('table', fn($q) => $q->where('created_by', $userId))
            ->where('status', 'Open')
            ->get()
            ->keyBy('table_id');

        $result = $tables->map(function ($table) use ($openSessions) {
            $session = $openSessions->get($table->id);
            return [
                'id'         => $table->id,
                'table_name' => $table->table_name,
                'max_seats'  => $table->max_seats,
                'status'     => $session ? 'Occupied' : 'Available',
                'order_no'   => $session?->order_no,
                'covers'     => $session?->covers ?? 0,
                'waiter'     => $session?->waiter_name,
                'opened_at'  => $session?->opened_at?->toDateTimeString(),
            ];
        });

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function waiters()
    {
        $waiters = Waiter::forUser(auth()->id())
            ->orderBy('name')
            ->select(['id', 'name', 'waiter_code', 'mobile'])
            ->get();
        return response()->json(['success' => true, 'data' => $waiters]);
    }
}
