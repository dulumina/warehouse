<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $permissions = Permission::paginate(15);

        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'feature' => ['string', 'max:255'],
        ]);

        $permission = Permission::create([
            'name' => $validated['name'],
            'feature' => $validated['feature'] ?? null,
            'guard_name' => 'web',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission created successfully',
            'data' => $permission
        ], 201);
    }

    /**
     * Display the specified permission
     */
    public function show(Permission $permission): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $permission
        ]);
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, Permission $permission): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
            'feature' => ['string', 'max:255'],
        ]);

        $permission->update([
            'name' => $validated['name'],
            'feature' => $validated['feature'] ?? $permission->feature,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully',
            'data' => $permission
        ]);
    }

    /**
     * Delete the specified permission
     */
    public function destroy(Permission $permission): \Illuminate\Http\JsonResponse
    {
        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully'
        ]);
    }
}
