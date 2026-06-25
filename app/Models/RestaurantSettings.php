<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class RestaurantSettings extends BaseModel
{

    protected $table = 'restaurant_settings';

    protected $fillable = [
        'created_by', 'business_unit', 'restaurant_name', 'gst_no',
        'bill_footer_text', 'guest_bill', 'last_accessed_by',
    ];
}
