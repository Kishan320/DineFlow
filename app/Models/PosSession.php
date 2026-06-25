<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class PosSession extends BaseModel
{
    protected $table = 'pos_sessions';

    protected $fillable = [
        'session_code', 'user_id', 'counter_name', 'branch_name',
        'opening_balance', 'closing_balance', 'total_sales',
        'total_cash', 'total_card', 'total_upi', 'total_others',
        'status', 'opened_at', 'closed_at', 'last_accessed_by',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'total_sales'     => 'decimal:2',
        'total_cash'      => 'decimal:2',
        'total_card'      => 'decimal:2',
        'total_upi'       => 'decimal:2',
        'total_others'    => 'decimal:2',
        'opened_at'       => 'datetime',
        'closed_at'       => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(PosOrder::class, 'session_id');
    }
}
