<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class RestaurantSettings extends Model
{
    use BelongsToUser;

    protected $table = 'restaurant_settings';

    protected $fillable = [
        'created_by', 'business_unit', 'restaurant_name', 'gst_no',
        'bill_footer_text', 'guest_bill', 'last_accessed_by',
    ];
}
