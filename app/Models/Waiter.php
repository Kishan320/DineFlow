<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Waiter extends BaseModel
{

    protected $fillable = ['created_by', 'waiter_code', 'name', 'mobile', 'dob', 'last_accessed_by'];
}
