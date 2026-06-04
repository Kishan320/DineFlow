<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Waiter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WaiterController extends Controller
{
    public function index(Request $request)
    {
        $userId  = auth()->id();
        $perPage = in_array((int)$request->per_page, [10, 25, 50, 100]) ? (int)$request->per_page : 10;
        $search  = trim($request->get('search', ''));

        $query = Waiter::forUser($userId)
            ->select(['id', 'waiter_code', 'name', 'mobile', 'dob', 'last_accessed_by', 'updated_at'])
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('waiter_code', 'LIKE', "%{$search}%")
                  ->orWhere('mobile', 'LIKE', "%{$search}%");
            }))
            ->orderByDesc('id');

        $waiters = $query->paginate($perPage, ['*'], 'page', (int)$request->get('page', 1));

        return response()->json([
            'success'      => true,
            'message'      => 'Waiters fetched successfully',
            'data'         => $waiters->items(),
            'current_page' => $waiters->currentPage(),
            'per_page'     => $waiters->perPage(),
            'total'        => $waiters->total(),
            'last_page'    => $waiters->lastPage(),
            'from'         => $waiters->firstItem(),
            'to'           => $waiters->lastItem(),
        ]);
    }

    public function store(Request $request)
    {
        $userId = auth()->id();
        $request->validate([
            'waiter_code' => ['required', 'string', 'max:50', Rule::unique('waiters')->where('created_by', $userId)],
            'name'        => 'required|string|max:255',
            'mobile'      => 'required|string|max:20',
            'dob'         => 'nullable|date',
        ]);

        $waiter = Waiter::create([
            'created_by'       => $userId,
            'waiter_code'      => $request->waiter_code,
            'name'             => $request->name,
            'mobile'           => $request->mobile,
            'dob'              => $request->dob,
            'last_accessed_by' => auth()->user()->name,
        ]);

        return response()->json($waiter->fresh(), 201);
    }

    public function update(Request $request, $id)
    {
        $userId = auth()->id();
        $waiter = Waiter::forUser($userId)->findOrFail($id);

        $request->validate([
            'waiter_code' => ['required', 'string', 'max:50', Rule::unique('waiters')->where('created_by', $userId)->ignore($waiter->id)],
            'name'        => 'required|string|max:255',
            'mobile'      => 'required|string|max:20',
            'dob'         => 'nullable|date',
        ]);

        $waiter->update([
            'waiter_code'      => $request->waiter_code,
            'name'             => $request->name,
            'mobile'           => $request->mobile,
            'dob'              => $request->dob,
            'last_accessed_by' => auth()->user()->name,
        ]);

        return response()->json($waiter->fresh());
    }

    public function destroy($id)
    {
        $waiter = Waiter::forUser(auth()->id())->findOrFail($id);
        $waiter->delete();
        return response()->json(null, 204);
    }
}
