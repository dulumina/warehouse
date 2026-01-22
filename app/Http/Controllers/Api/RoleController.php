<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $roles = Role::with('permissions')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name']
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $role->givePermissionTo($validated['permissions']);
        }

        $role->load('permissions');

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data' => $role
        ], 201);
    }

    /**
     * Display the specified role
     */
    public function show(Role $role): \Illuminate\Http\JsonResponse
    {
        $role->load('permissions');

        return response()->json([
            'success' => true,
            'data' => $role
        ]);
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
        ]);

        $role->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'data' => $role
        ]);
    }

    /**
     * Delete the specified role
     */
    public function destroy(Role $role): \Illuminate\Http\JsonResponse
    {
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully'
        ]);
    }

    /**
     * Assign permissions to a role
     */
    public function assignPermissions(Request $request, Role $role): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name']
        ]);

        $role->syncPermissionTo($validated['permissions']);
        $role->load('permissions');

        return response()->json([
            'success' => true,
            'message' => 'Permissions assigned successfully',
            'data' => $role
        ]);
    }
}
