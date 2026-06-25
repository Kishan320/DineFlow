<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends BaseModel
{

    protected $table = 'restaurant_tables';

    protected $fillable = ['created_by', 'table_name', 'description', 'max_seats', 'status', 'last_accessed_by'];
}
