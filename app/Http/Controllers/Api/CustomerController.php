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
        $request->validate([
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

        $customer = new Customer();
        $customer->code              = $request->code;
        $customer->company_name      = $request->company_name;
        $customer->contact_person    = $request->contact_person;
        $customer->email             = $request->email;
        $customer->mobile            = $request->mobile;
        $customer->tax_number        = $request->tax_number;
        $customer->payment_terms     = $request->payment_terms;
        $customer->billing_name      = $request->billing_name;
        $customer->billing_address   = $request->billing_address;
        $customer->billing_address2  = $request->billing_address2;
        $customer->billing_city      = $request->billing_city;
        $customer->billing_state     = $request->billing_state;
        $customer->billing_country   = $request->billing_country;
        $customer->billing_zip       = $request->billing_zip;
        $customer->same_as_billing   = $request->same_as_billing;
        $customer->shipping_name     = $request->shipping_name;
        $customer->shipping_address  = $request->shipping_address;
        $customer->shipping_address2 = $request->shipping_address2;
        $customer->shipping_city     = $request->shipping_city;
        $customer->shipping_state    = $request->shipping_state;
        $customer->shipping_country  = $request->shipping_country;
        $customer->shipping_zip      = $request->shipping_zip;
        $customer->notes             = $request->notes;
        $customer->last_accessed_by  = 'Administrator';
        $customer->save();
        $customer->refresh();
        return response()->json($customer, 201);
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
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

        $customer->code              = $request->code;
        $customer->company_name      = $request->company_name;
        $customer->contact_person    = $request->contact_person;
        $customer->email             = $request->email;
        $customer->mobile            = $request->mobile;
        $customer->tax_number        = $request->tax_number;
        $customer->payment_terms     = $request->payment_terms;
        $customer->billing_name      = $request->billing_name;
        $customer->billing_address   = $request->billing_address;
        $customer->billing_address2  = $request->billing_address2;
        $customer->billing_city      = $request->billing_city;
        $customer->billing_state     = $request->billing_state;
        $customer->billing_country   = $request->billing_country;
        $customer->billing_zip       = $request->billing_zip;
        $customer->same_as_billing   = $request->same_as_billing;
        $customer->shipping_name     = $request->shipping_name;
        $customer->shipping_address  = $request->shipping_address;
        $customer->shipping_address2 = $request->shipping_address2;
        $customer->shipping_city     = $request->shipping_city;
        $customer->shipping_state    = $request->shipping_state;
        $customer->shipping_country  = $request->shipping_country;
        $customer->shipping_zip      = $request->shipping_zip;
        $customer->notes             = $request->notes;
        $customer->last_accessed_by  = 'Administrator';
        $customer->save();
        $customer->refresh();
        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(null, 204);
    }
}
