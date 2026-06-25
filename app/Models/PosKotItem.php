<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class PosKotItem extends BaseModel
{
    protected $table = 'pos_kot_items';

    protected $fillable = [
        'pos_kot_id', 'item_id', 'item_name', 'quantity', 'notes',
    ];

    public function kot()
    {
        return $this->belongsTo(PosKot::class, 'pos_kot_id');
    }
}
