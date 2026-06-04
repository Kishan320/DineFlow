<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class PosCustomerController extends Controller
{
    public function search(Request $request)
    {
        $userId = auth()->id();
        $search = trim($request->search ?? '');

        $customers = Customer::forUser($userId)
            ->select(['id', 'code', 'company_name', 'contact_person', 'mobile', 'email', 'billing_address', 'billing_city'])
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            }))
            ->orderBy('company_name')
            ->limit(20)
            ->get()
            ->map(fn($c) => [
                'id'      => $c->id,
                'name'    => $c->company_name,
                'contact' => $c->contact_person,
                'mobile'  => $c->mobile,
                'email'   => $c->email,
                'address' => trim(implode(', ', array_filter([$c->billing_address, $c->billing_city]))),
            ]);

        $walkIn = ['id' => null, 'name' => 'Walk-In Guest', 'contact' => null, 'mobile' => null, 'email' => null, 'address' => null];

        return response()->json(['success' => true, 'data' => $customers->prepend($walkIn)->values()]);
    }

    public function store(Request $request)
    {
        $userId = auth()->id();
        $request->validate([
            'company_name'    => 'required|string|max:255',
            'mobile'          => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'contact_person'  => 'nullable|string|max:255',
            'billing_address' => 'nullable|string|max:500',
        ]);

        $count    = Customer::forUser($userId)->count();
        $customer = Customer::create([
            'created_by'      => $userId,
            'code'            => 'CUST-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT),
            'company_name'    => $request->company_name,
            'contact_person'  => $request->contact_person,
            'mobile'          => $request->mobile,
            'email'           => $request->email,
            'billing_address' => $request->billing_address,
            'last_accessed_by' => auth()->user()->name,
        ]);

        return response()->json(['success' => true, 'data' => [
            'id'      => $customer->id,
            'name'    => $customer->company_name,
            'contact' => $customer->contact_person,
            'mobile'  => $customer->mobile,
            'email'   => $customer->email,
            'address' => $customer->billing_address,
        ]], 201);
    }
}
