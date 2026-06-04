<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaxController extends Controller
{
    public function index(Request $request)
    {
        $userId  = auth()->id();
        $perPage = in_array((int)$request->per_page, [10, 25, 50, 100]) ? (int)$request->per_page : 10;
        $search  = trim($request->get('search', ''));

        $query = Tax::forUser($userId)
            ->select(['id', 'hsn_code', 'description', 'cgst', 'sgst', 'igst', 'tax_percent', 'last_accessed_by', 'updated_at'])
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('hsn_code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            }))
            ->orderByDesc('id');

        $taxes = $query->paginate($perPage, ['*'], 'page', (int)$request->get('page', 1));

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

    public function list()
    {
        $taxes = Tax::forUser(auth()->id())
            ->select('id', 'description', 'tax_percent')
            ->orderBy('description')
            ->get();
        return response()->json(['success' => true, 'data' => $taxes]);
    }

    public function store(Request $request)
    {
        $userId = auth()->id();
        $request->validate([
            'hsn_code'        => ['required', 'string', 'max:50', Rule::unique('taxes')->where('created_by', $userId)],
            'description'     => 'required|string|max:255',
            'cgst'            => 'required|numeric|min:0',
            'sgst'            => 'required|numeric|min:0',
            'igst'            => 'nullable|numeric|min:0',
            'cess'            => 'nullable|numeric|min:0',
            'additional_cess' => 'nullable|numeric|min:0',
            'vat'             => 'required|numeric|min:0',
            'tax_percent'     => 'required|numeric|min:0',
        ]);

        $tax = Tax::create(array_merge(
            $request->only(['hsn_code', 'description', 'cgst', 'sgst', 'igst', 'cess', 'additional_cess', 'vat', 'tax_percent']),
            ['created_by' => $userId, 'last_accessed_by' => auth()->user()->name]
        ));

        return response()->json($tax->fresh(), 201);
    }

    public function update(Request $request, $id)
    {
        $userId = auth()->id();
        $tax    = Tax::forUser($userId)->findOrFail($id);

        $request->validate([
            'hsn_code'        => ['required', 'string', 'max:50', Rule::unique('taxes')->where('created_by', $userId)->ignore($tax->id)],
            'description'     => 'required|string|max:255',
            'cgst'            => 'required|numeric|min:0',
            'sgst'            => 'required|numeric|min:0',
            'igst'            => 'nullable|numeric|min:0',
            'cess'            => 'nullable|numeric|min:0',
            'additional_cess' => 'nullable|numeric|min:0',
            'vat'             => 'required|numeric|min:0',
            'tax_percent'     => 'required|numeric|min:0',
        ]);

        $tax->update(array_merge(
            $request->only(['hsn_code', 'description', 'cgst', 'sgst', 'igst', 'cess', 'additional_cess', 'vat', 'tax_percent']),
            ['last_accessed_by' => auth()->user()->name]
        ));

        return response()->json($tax->fresh());
    }

    public function destroy($id)
    {
        $tax = Tax::forUser(auth()->id())->findOrFail($id);
        $tax->delete();
        return response()->json(null, 204);
    }
}
