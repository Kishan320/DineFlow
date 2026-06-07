<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:roles.view')->only(['index', 'show', 'permissions']);
        $this->middleware('permission:roles.manage')->only(['store', 'update', 'destroy', 'syncPermissions']);
    }

    /** GET /api/roles — List all roles with permission counts. */
    public function index()
    {
        $roles = Role::where('guard_name', 'sanctum')
            ->withCount('permissions')
            ->with('permissions:id,name')
            ->get();

        return response()->json(['success' => true, 'data' => $roles]);
    }

    /** GET /api/roles/{role} — Single role with permissions. */
    public function show(Role $role)
    {
        $role->load('permissions:id,name');
        return response()->json(['success' => true, 'data' => $role]);
    }

    /** POST /api/roles — Create a new role. */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'sanctum']);

        if ($request->filled('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        $role->load('permissions:id,name');
        return response()->json(['success' => true, 'data' => $role], 201);
    }

    /** PUT /api/roles/{role} — Update role name. */
    public function update(Request $request, Role $role)
    {
        // Prevent renaming system roles
        if (in_array($role->name, ['Super Admin', 'Admin'])) {
            return response()->json(['success' => false, 'message' => 'System roles cannot be renamed.'], 403);
        }

        $request->validate([
            'name' => "required|string|max:100|unique:roles,name,{$role->id}",
        ]);

        $role->update(['name' => $request->name]);
        return response()->json(['success' => true, 'data' => $role]);
    }

    /** DELETE /api/roles/{role} — Delete a role. */
    public function destroy(Role $role)
    {
        if (in_array($role->name, ['Super Admin', 'Admin'])) {
            return response()->json(['success' => false, 'message' => 'System roles cannot be deleted.'], 403);
        }

        $role->delete();
        return response()->json(null, 204);
    }

    /** GET /api/roles/{role}/permissions — Get permissions for a role. */
    public function permissions(Role $role)
    {
        return response()->json([
            'success' => true,
            'data'    => $role->permissions()->pluck('name'),
        ]);
    }

    /** POST /api/roles/{role}/permissions — Sync permissions to a role. */
    public function syncPermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions'   => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        if ($role->name === 'Super Admin') {
            return response()->json(['success' => false, 'message' => 'Super Admin permissions cannot be modified.'], 403);
        }

        $role->syncPermissions($request->permissions);

        return response()->json([
            'success' => true,
            'message' => 'Permissions synced successfully.',
            'data'    => $role->permissions()->pluck('name'),
        ]);
    }

    /** GET /api/permissions — List all permissions grouped by module. */
    public function allPermissions()
    {
        $permissions = Permission::where('guard_name', 'sanctum')
            ->orderBy('name')
            ->pluck('name');

        // Group by module prefix (e.g. "categories.view" → "categories")
        $grouped = $permissions->groupBy(function ($perm) {
            return explode('.', $perm)[0];
        });

        return response()->json(['success' => true, 'data' => $grouped]);
    }
}
