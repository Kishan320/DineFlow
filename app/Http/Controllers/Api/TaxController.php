<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        return response()->json(Tax::orderBy('hsn_code')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'hsn_code'       => 'required|string|max:50|unique:taxes,hsn_code',
            'description'    => 'required|string|max:255',
            'cgst'           => 'required|numeric|min:0',
            'sgst'           => 'required|numeric|min:0',
            'igst'           => 'nullable|numeric|min:0',
            'cess'           => 'nullable|numeric|min:0',
            'additional_cess'=> 'nullable|numeric|min:0',
            'vat'            => 'required|numeric|min:0',
            'tax_percent'    => 'required|numeric|min:0',
        ]);

        $data['last_accessed_by'] = 'Administrator';
        return response()->json(Tax::create($data), 201);
    }

    public function update(Request $request, Tax $tax)
    {
        $data = $request->validate([
            'hsn_code'       => 'required|string|max:50|unique:taxes,hsn_code,' . $tax->id,
            'description'    => 'required|string|max:255',
            'cgst'           => 'required|numeric|min:0',
            'sgst'           => 'required|numeric|min:0',
            'igst'           => 'nullable|numeric|min:0',
            'cess'           => 'nullable|numeric|min:0',
            'additional_cess'=> 'nullable|numeric|min:0',
            'vat'            => 'required|numeric|min:0',
            'tax_percent'    => 'required|numeric|min:0',
        ]);

        $data['last_accessed_by'] = 'Administrator';
        $tax->update($data);
        return response()->json($tax);
    }

    public function destroy(Tax $tax)
    {
        $tax->delete();
        return response()->json(null, 204);
    }
}
