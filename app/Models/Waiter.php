<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waiter extends Model
{
    protected $fillable = ['waiter_code', 'name', 'mobile', 'dob', 'last_accessed_by'];
}
