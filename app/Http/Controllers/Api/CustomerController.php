<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return response()->json(Customer::orderBy('company_name')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'              => 'required|string|max:50|unique:customers,code',
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

        return response()->json(Customer::create($data), 201);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'code'              => 'required|string|max:50|unique:customers,code,' . $customer->id,
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

        $customer->update($data);
        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(null, 204);
    }
}
