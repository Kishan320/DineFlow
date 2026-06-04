<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosOrderItem extends Model
{
    protected $table = 'pos_order_items';

    protected $fillable = [
        'pos_order_id', 'item_id', 'item_name', 'item_code', 'category',
        'unit_price', 'quantity', 'discount', 'discount_type',
        'tax_amount', 'tax_percent', 'tax_type', 'line_total',
        'notes', 'kot_printed',
    ];

    protected $casts = [
        'unit_price'  => 'decimal:2',
        'discount'    => 'decimal:2',
        'tax_amount'  => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'line_total'  => 'decimal:2',
        'kot_printed' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
