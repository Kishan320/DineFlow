<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Category extends BaseModel
{

    protected $fillable = ['created_by', 'category_name', 'description', 'last_accessed_by'];
}
