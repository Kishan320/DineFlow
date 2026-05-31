<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    protected $table = 'restaurant_tables';

    protected $fillable = ['table_name', 'description', 'max_seats', 'last_accessed_by'];
}
