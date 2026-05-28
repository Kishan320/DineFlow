<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosOrder extends Model
{
    protected $fillable = [
        'order_no', 'customer_id', 'customer_name', 'customer_phone',
        'table_id', 'table_label', 'waiter_id', 'waiter_name',
        'order_type', 'covers', 'subtotal', 'tax_amount', 'discount',
        'total', 'round_off', 'net_payable', 'status', 'payment_status',
        'bill_pay_type', 'cash_amt', 'card_ref', 'card_amt',
        'others_type', 'others_ref', 'others_amt', 'payment_note', 'notes',
    ];

    public function items()
    {
        return $this->hasMany(PosOrderItem::class);
    }

    public function kots()
    {
        return $this->hasMany(PosKot::class);
    }
}
