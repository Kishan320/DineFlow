<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosSession;
use App\Services\PosService;
use Illuminate\Http\Request;

class PosSessionController extends Controller
{
    public function __construct(private PosService $posService)
    {
        $this->middleware('permission:pos.session.view')->only(['active', 'show']);
        $this->middleware('permission:pos.session.open')->only('open');
        $this->middleware('permission:pos.session.close')->only('close');
        $this->middleware('permission:pos.session.delete')->only('destroy');
    }


    public function active(Request $request)
    {
        $userId  = $request->user()?->id;
        $session = $this->posService->getActiveSession($userId);

        return response()->json([
            'success' => true,
            'data'    => $session,
        ]);
    }

    public function open(Request $request)
    {
        $request->validate([
            'opening_balance' => 'numeric|min:0',
            'counter_name'    => 'nullable|string|max:100',
            'branch_name'     => 'nullable|string|max:100',
        ]);

        $session = $this->posService->openSession([
            'user_id'          => $request->user()?->id,
            'counter_name'     => $request->counter_name ?? 'Main Counter',
            'branch_name'      => $request->branch_name ?? 'Main Branch',
            'opening_balance'  => $request->opening_balance ?? 0,
            'last_accessed_by' => $request->user()?->name ?? 'Administrator',
        ]);

        return response()->json(['success' => true, 'data' => $session], 201);
    }

    public function close(Request $request, PosSession $session)
    {
        $request->validate(['closing_balance' => 'required|numeric|min:0']);

        if ($session->status === 'Closed') {
            return response()->json(['success' => false, 'message' => 'Session already closed'], 422);
        }

        $closed = $this->posService->closeSession($session, (float) $request->closing_balance);

        return response()->json(['success' => true, 'data' => $closed]);
    }

    public function destroy(PosSession $session)
    {
        if ($session->status === 'Open') {
            $this->posService->closeSession($session, 0);
        }
        $session->delete();
        return response()->json(null, 204);
    }

    public function show(PosSession $session)
    {
        $session->loadCount('orders');
        return response()->json(['success' => true, 'data' => $session]);
    }
}
