<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $userId  = auth()->id();
        $perPage = in_array((int)$request->per_page, [10, 25, 50, 100]) ? (int)$request->per_page : 10;
        $search  = trim($request->get('search', ''));

        $query = Customer::forUser($userId)
            ->select(['id', 'code', 'company_name', 'contact_person', 'email', 'mobile', 'last_accessed_by', 'updated_at'])
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('company_name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('contact_person', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('mobile', 'LIKE', "%{$search}%");
            }))
            ->orderByDesc('id');

        $customers = $query->paginate($perPage, ['*'], 'page', (int)$request->get('page', 1));

        return response()->json([
            'success'      => true,
            'message'      => 'Customers fetched successfully',
            'data'         => $customers->items(),
            'current_page' => $customers->currentPage(),
            'per_page'     => $customers->perPage(),
            'total'        => $customers->total(),
            'last_page'    => $customers->lastPage(),
            'from'         => $customers->firstItem(),
            'to'           => $customers->lastItem(),
        ]);
    }

    public function store(Request $request)
    {
        $userId = auth()->id();
        $request->validate([
            'code'              => ['required', 'string', 'max:50', Rule::unique('customers')->where('created_by', $userId)],
            'company_name'      => 'required|string|max:255',
            'contact_person'    => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',
            'mobile'            => 'nullable|string|max:20',
            'tax_number'        => 'nullable|string|max:100',
            'payment_terms'     => 'nullable|string|max:50',
            'billing_name'      => 'nullable|string|max:255',
            'billing_address'   => 'nullable|string|max:500',
            'billing_address2'  => 'nullable|string|max:500',
            'billing_city'      => 'nullable|string|max:100',
            'billing_state'     => 'nullable|string|max:100',
            'billing_country'   => 'nullable|string|max:100',
            'billing_zip'       => 'nullable|string|max:20',
            'same_as_billing'   => 'boolean',
            'shipping_name'     => 'nullable|string|max:255',
            'shipping_address'  => 'nullable|string|max:500',
            'shipping_address2' => 'nullable|string|max:500',
            'shipping_city'     => 'nullable|string|max:100',
            'shipping_state'    => 'nullable|string|max:100',
            'shipping_country'  => 'nullable|string|max:100',
            'shipping_zip'      => 'nullable|string|max:20',
            'notes'             => 'nullable|string',
        ]);

        $customer = Customer::create(array_merge(
            $request->only([
                'code', 'company_name', 'contact_person', 'email', 'mobile', 'tax_number',
                'payment_terms', 'billing_name', 'billing_address', 'billing_address2',
                'billing_city', 'billing_state', 'billing_country', 'billing_zip',
                'same_as_billing', 'shipping_name', 'shipping_address', 'shipping_address2',
                'shipping_city', 'shipping_state', 'shipping_country', 'shipping_zip', 'notes',
            ]),
            ['created_by' => $userId, 'last_accessed_by' => auth()->user()->name]
        ));

        return response()->json($customer->fresh(), 201);
    }

    public function update(Request $request, $id)
    {
        $userId   = auth()->id();
        $customer = Customer::forUser($userId)->findOrFail($id);

        $request->validate([
            'code'              => ['required', 'string', 'max:50', Rule::unique('customers')->where('created_by', $userId)->ignore($customer->id)],
            'company_name'      => 'required|string|max:255',
            'contact_person'    => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',
            'mobile'            => 'nullable|string|max:20',
            'tax_number'        => 'nullable|string|max:100',
            'payment_terms'     => 'nullable|string|max:50',
            'billing_name'      => 'nullable|string|max:255',
            'billing_address'   => 'nullable|string|max:500',
            'billing_address2'  => 'nullable|string|max:500',
            'billing_city'      => 'nullable|string|max:100',
            'billing_state'     => 'nullable|string|max:100',
            'billing_country'   => 'nullable|string|max:100',
            'billing_zip'       => 'nullable|string|max:20',
            'same_as_billing'   => 'boolean',
            'shipping_name'     => 'nullable|string|max:255',
            'shipping_address'  => 'nullable|string|max:500',
            'shipping_address2' => 'nullable|string|max:500',
            'shipping_city'     => 'nullable|string|max:100',
            'shipping_state'    => 'nullable|string|max:100',
            'shipping_country'  => 'nullable|string|max:100',
            'shipping_zip'      => 'nullable|string|max:20',
            'notes'             => 'nullable|string',
        ]);

        $customer->update(array_merge(
            $request->only([
                'code', 'company_name', 'contact_person', 'email', 'mobile', 'tax_number',
                'payment_terms', 'billing_name', 'billing_address', 'billing_address2',
                'billing_city', 'billing_state', 'billing_country', 'billing_zip',
                'same_as_billing', 'shipping_name', 'shipping_address', 'shipping_address2',
                'shipping_city', 'shipping_state', 'shipping_country', 'shipping_zip', 'notes',
            ]),
            ['last_accessed_by' => auth()->user()->name]
        ));

        return response()->json($customer->fresh());
    }

    public function destroy($id)
    {
        $customer = Customer::forUser(auth()->id())->findOrFail($id);
        $customer->delete();
        return response()->json(null, 204);
    }
}
