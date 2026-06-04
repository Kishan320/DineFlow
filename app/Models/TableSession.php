<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableSession extends Model
{
    protected $table = 'table_sessions';

    protected $fillable = [
        'table_id', 'pos_order_id', 'order_no', 'status',
        'covers', 'waiter_id', 'waiter_name',
        'opened_at', 'closed_at', 'last_accessed_by',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function order()
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }
}
