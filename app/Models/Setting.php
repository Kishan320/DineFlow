<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Setting extends BaseModel
{

    protected $fillable = [
        'key',
        'value',
        'created_by',
    ];

    /**
     * User relation
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get setting by key for logged-in user
     */
}
