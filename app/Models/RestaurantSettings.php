<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantSettings extends Model
{
    protected $table = 'restaurant_settings';
    protected $fillable = ['business_unit', 'restaurant_name', 'gst_no', 'bill_footer_text', 'guest_bill', 'last_accessed_by'];
}
