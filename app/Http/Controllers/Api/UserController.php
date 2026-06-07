<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:users.view')->only(['index', 'show']);
        $this->middleware('permission:users.manage')->only(['store', 'update', 'destroy', 'assignRole']);
    }

    /**
     * GET /api/users — List users in the same restaurant.
     * Scoped to the authenticated user's restaurant_id.
     */
    public function index(Request $request)
    {
        $restaurantId = $request->user()->restaurant_id;

        $users = User::where('restaurant_id', $restaurantId)
            ->with('roles:id,name')
            ->select('id', 'name', 'email', 'restaurant_id', 'created_at')
            ->orderByDesc('id')
            ->get()
            ->map(function ($user) {
                return [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'roles'      => $user->roles->pluck('name'),
                    'created_at' => $user->created_at->toDateTimeString(),
                ];
            });

        return response()->json(['success' => true, 'data' => $users]);
    }

    /** GET /api/users/{user} — Single user detail. */
    public function show(Request $request, User $user)
    {
        // Ensure user belongs to same restaurant
        if ($user->restaurant_id !== $request->user()->restaurant_id) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'roles'       => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'created_at'  => $user->created_at->toDateTimeString(),
            ],
        ]);
    }

    /**
     * POST /api/users — Create a new staff user under the same restaurant.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::min(8)],
            'role'     => ['required', 'string', 'exists:roles,name'],
        ]);

        $authUser = $request->user();

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'restaurant_id' => $authUser->restaurant_id,
        ]);

        $user->assignRole($request->role);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],
        ], 201);
    }

    /**
     * PUT /api/users/{user} — Update user info and/or role.
     */
    public function update(Request $request, User $user)
    {
        // Scoping check
        if ($user->restaurant_id !== $request->user()->restaurant_id) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        $request->validate([
            'name'     => ['sometimes', 'required', 'string', 'max:255'],
            'email'    => ["sometimes", "required", "email", "unique:users,email,{$user->id}"],
            'password' => ['nullable', Rules\Password::min(8)],
            'role'     => ['sometimes', 'required', 'string', 'exists:roles,name'],
        ]);

        $data = $request->only(['name', 'email']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    /**
     * DELETE /api/users/{user} — Remove a staff user.
     */
    public function destroy(Request $request, User $user)
    {
        if ($user->restaurant_id !== $request->user()->restaurant_id) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        // Prevent self-deletion
        if ($user->id === $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'You cannot delete your own account.'], 422);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(null, 204);
    }

    /**
     * POST /api/users/{user}/role — Assign a role to a user.
     */
    public function assignRole(Request $request, User $user)
    {
        if ($user->restaurant_id !== $request->user()->restaurant_id) {
            return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        }

        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user->syncRoles([$request->role]);

        return response()->json([
            'success' => true,
            'message' => 'Role assigned successfully.',
            'roles'   => $user->getRoleNames(),
        ]);
    }
}
