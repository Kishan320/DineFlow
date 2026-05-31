<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'code', 'item_name', 'category', 'restaurant_price', 'bar_price', 'room_price',
        'tax_type', 'tax', 'state', 'item_type', 'note', 'image_url', 'last_accessed_by',
    ];
}
