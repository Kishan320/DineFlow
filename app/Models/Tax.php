<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use BelongsToUser;

    protected $fillable = [
        'created_by', 'hsn_code', 'description', 'cgst', 'sgst', 'igst',
        'cess', 'additional_cess', 'vat', 'tax_percent', 'last_accessed_by',
    ];
}
