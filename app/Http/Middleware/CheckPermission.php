<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Checks if the authenticated user has the required permission.
     * Returns a clean JSON 403 (never 404) so the frontend can handle it properly.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! $request->user()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (! $request->user()->hasPermissionTo($permission)) {
            return response()->json([
                'success'    => false,
                'message'    => "You don't have permission to perform this action.",
                'permission' => $permission,
            ], 403);
        }

        return $next($request);
    }
}
