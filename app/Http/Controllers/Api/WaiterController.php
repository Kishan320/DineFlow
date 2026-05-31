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
        $request->validate([
            'waiter_code' => 'required|string|max:50|unique:waiters,waiter_code',
            'name'        => 'required|string|max:255',
            'mobile'      => 'required|string|max:20',
            'dob'         => 'nullable|date',
        ]);

        $waiter = new Waiter();
        $waiter->waiter_code     = $request->waiter_code;
        $waiter->name            = $request->name;
        $waiter->mobile          = $request->mobile;
        $waiter->dob             = $request->dob;
        $waiter->last_accessed_by = 'Administrator';
        $waiter->save();
        $waiter->refresh();
        return response()->json($waiter, 201);
    }

    public function update(Request $request, Waiter $waiter)
    {
        $request->validate([
            'waiter_code' => 'required|string|max:50|unique:waiters,waiter_code,' . $waiter->id,
            'name'        => 'required|string|max:255',
            'mobile'      => 'required|string|max:20',
            'dob'         => 'nullable|date',
        ]);

        $waiter->waiter_code     = $request->waiter_code;
        $waiter->name            = $request->name;
        $waiter->mobile          = $request->mobile;
        $waiter->dob             = $request->dob;
        $waiter->last_accessed_by = 'Administrator';
        $waiter->save();
        $waiter->refresh();
        return response()->json($waiter);
    }

    public function destroy(Waiter $waiter)
    {
        $waiter->delete();
        return response()->json(null, 204);
    }
}
