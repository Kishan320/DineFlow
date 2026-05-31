<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Waiter;
use Illuminate\Http\Request;

class WaiterController extends Controller
{
    public function index()
    {
        return response()->json(Waiter::orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'waiter_code' => 'required|string|max:50|unique:waiters,waiter_code',
            'name'        => 'required|string|max:255',
            'mobile'      => 'required|string|max:20',
            'dob'         => 'nullable|date',
        ]);

        return response()->json(Waiter::create($data), 201);
    }

    public function update(Request $request, Waiter $waiter)
    {
        $data = $request->validate([
            'waiter_code' => 'required|string|max:50|unique:waiters,waiter_code,' . $waiter->id,
            'name'        => 'required|string|max:255',
            'mobile'      => 'required|string|max:20',
            'dob'         => 'nullable|date',
        ]);

        $waiter->update($data);
        return response()->json($waiter);
    }

    public function destroy(Waiter $waiter)
    {
        $waiter->delete();
        return response()->json(null, 204);
    }
}
