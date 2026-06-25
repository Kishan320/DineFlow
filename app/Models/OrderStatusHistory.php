<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends BaseModel
{
    protected $table = 'order_status_histories';

    protected $fillable = [
        'pos_order_id', 'from_status', 'to_status', 'changed_by', 'notes',
    ];

    public function order()
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }
}
