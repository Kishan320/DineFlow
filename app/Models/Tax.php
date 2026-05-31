<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'hsn_code', 'description', 'cgst', 'sgst', 'igst',
        'cess', 'additional_cess', 'vat', 'tax_percent', 'last_accessed_by',
    ];
}
