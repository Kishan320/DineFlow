<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Waiter;
use Illuminate\Http\Request;

class WaiterController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        if (!in_array($perPage, [10, 25, 100])) {
            $perPage = 10;
        }

        $search = trim($request->get('search', ''));
        $page = (int) $request->get('page', 1);

        $query = Waiter::query()
            ->select(['id', 'waiter_code', 'name', 'mobile', 'dob', 'last_accessed_by', 'updated_at']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('waiter_code', 'LIKE', "%{$search}%")
                  ->orWhere('mobile', 'LIKE', "%{$search}%")
                  ->orWhere('last_accessed_by', 'LIKE', "%{$search}%");
            });
        }

        $query->orderByDesc('id');
        $waiters = $query->paginate($perPage, ['*'], 'page', $page);

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
