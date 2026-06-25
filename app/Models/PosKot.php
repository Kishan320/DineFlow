<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class PosKot extends BaseModel
{
    protected $table = 'pos_kots';

    protected $fillable = [
        'kot_no', 'pos_order_id', 'order_no', 'table_label',
        'order_type', 'customer_name', 'waiter_name', 'notes',
        'status', 'last_accessed_by',
    ];

    public function order()
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }

    public function items()
    {
        return $this->hasMany(PosKotItem::class, 'pos_kot_id');
    }
}
