<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use BelongsToUser;

    protected $fillable = [
        'user_id', 'created_by', 'code', 'company_name', 'contact_person', 'email', 'mobile', 'tax_number',
        'payment_terms', 'billing_name', 'billing_address', 'billing_address2',
        'billing_city', 'billing_state', 'billing_country', 'billing_zip',
        'same_as_billing', 'shipping_name', 'shipping_address', 'shipping_address2',
        'shipping_city', 'shipping_state', 'shipping_country', 'shipping_zip', 'notes', 'last_accessed_by',
    ];

    protected $casts = ['same_as_billing' => 'boolean'];
}
