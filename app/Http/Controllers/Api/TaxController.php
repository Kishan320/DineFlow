<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $search = trim($request->get('search', ''));
        $page = (int) $request->get('page', 1);

        $query = Tax::query()
            ->select(['id', 'hsn_code', 'description', 'cgst', 'sgst', 'igst', 'tax_percent', 'last_accessed_by', 'updated_at']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('hsn_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('last_accessed_by', 'LIKE', "%{$search}%");
            });
        }

        $query->orderByDesc('id');
        $taxes = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success'      => true,
            'message'      => 'Taxes fetched successfully',
            'data'         => $taxes->items(),
            'current_page' => $taxes->currentPage(),
            'per_page'     => $taxes->perPage(),
            'total'        => $taxes->total(),
            'last_page'    => $taxes->lastPage(),
            'from'         => $taxes->firstItem(),
            'to'           => $taxes->lastItem(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'hsn_code'        => 'required|string|max:50|unique:taxes,hsn_code',
            'description'     => 'required|string|max:255',
            'cgst'            => 'required|numeric|min:0',
            'sgst'            => 'required|numeric|min:0',
            'igst'            => 'nullable|numeric|min:0',
            'cess'            => 'nullable|numeric|min:0',
            'additional_cess' => 'nullable|numeric|min:0',
            'vat'             => 'required|numeric|min:0',
            'tax_percent'     => 'required|numeric|min:0',
        ]);

        $tax = new Tax();
        $tax->hsn_code        = $request->hsn_code;
        $tax->description     = $request->description;
        $tax->cgst            = $request->cgst;
        $tax->sgst            = $request->sgst;
        $tax->igst            = $request->igst;
        $tax->cess            = $request->cess;
        $tax->additional_cess = $request->additional_cess;
        $tax->vat             = $request->vat;
        $tax->tax_percent     = $request->tax_percent;
        $tax->last_accessed_by = 'Administrator';
        $tax->save();
        $tax->refresh();
        return response()->json($tax, 201);
    }

    public function update(Request $request, Tax $tax)
    {
        $request->validate([
            'hsn_code'        => 'required|string|max:50|unique:taxes,hsn_code,' . $tax->id,
            'description'     => 'required|string|max:255',
            'cgst'            => 'required|numeric|min:0',
            'sgst'            => 'required|numeric|min:0',
            'igst'            => 'nullable|numeric|min:0',
            'cess'            => 'nullable|numeric|min:0',
            'additional_cess' => 'nullable|numeric|min:0',
            'vat'             => 'required|numeric|min:0',
            'tax_percent'     => 'required|numeric|min:0',
        ]);

        $tax->hsn_code        = $request->hsn_code;
        $tax->description     = $request->description;
        $tax->cgst            = $request->cgst;
        $tax->sgst            = $request->sgst;
        $tax->igst            = $request->igst;
        $tax->cess            = $request->cess;
        $tax->additional_cess = $request->additional_cess;
        $tax->vat             = $request->vat;
        $tax->tax_percent     = $request->tax_percent;
        $tax->last_accessed_by = 'Administrator';
        $tax->save();
        $tax->refresh();
        return response()->json($tax);
    }

    public function destroy(Tax $tax)
    {
        $tax->delete();
        return response()->json(null, 204);
    }
}
