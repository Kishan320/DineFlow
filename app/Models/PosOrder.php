<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosOrder extends Model
{
    protected $table = 'pos_orders';

    protected $fillable = [
        'order_no', 'invoice_no', 'session_id',
        'customer_id', 'customer_name', 'customer_phone',
        'table_id', 'table_label', 'waiter_id', 'waiter_name',
        'order_type', 'covers', 'notes',
        'delivery_address', 'delivery_charge', 'delivery_notes',
        'subtotal', 'tax_amount', 'tax_breakdown', 'discount', 'discount_type',
        'total', 'round_off', 'net_payable',
        'status', 'payment_status', 'bill_pay_type',
        'cash_amt', 'card_ref', 'card_amt',
        'others_type', 'others_ref', 'others_amt',
        'upi_amt', 'upi_ref', 'payment_note',
        'last_accessed_by',
    ];

    protected $casts = [
        'tax_breakdown'   => 'array',
        'subtotal'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'discount'        => 'decimal:2',
        'total'           => 'decimal:2',
        'round_off'       => 'decimal:2',
        'net_payable'     => 'decimal:2',
        'cash_amt'        => 'decimal:2',
        'card_amt'        => 'decimal:2',
        'others_amt'      => 'decimal:2',
        'upi_amt'         => 'decimal:2',
        'delivery_charge' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(PosOrderItem::class, 'pos_order_id');
    }

    public function kots()
    {
        return $this->hasMany(PosKot::class, 'pos_order_id');
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class, 'pos_order_id');
    }

    public function tableSession()
    {
        return $this->hasOne(TableSession::class, 'pos_order_id');
    }

    public function session()
    {
        return $this->belongsTo(PosSession::class, 'session_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function waiter()
    {
        return $this->belongsTo(Waiter::class, 'waiter_id');
    }
}
