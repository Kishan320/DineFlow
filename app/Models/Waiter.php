<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class Waiter extends Model
{
    use BelongsToUser;

    protected $fillable = ['created_by', 'waiter_code', 'name', 'mobile', 'dob', 'last_accessed_by'];
}
