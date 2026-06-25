<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Tax extends BaseModel
{

    protected $fillable = [
        'created_by', 'hsn_code', 'description', 'cgst', 'sgst', 'igst',
        'cess', 'additional_cess', 'vat', 'tax_percent', 'last_accessed_by',
    ];
}
