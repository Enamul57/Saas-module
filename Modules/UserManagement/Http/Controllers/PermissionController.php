<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Feature as Modules;
use App\Models\TenantRole as Role;

class PermissionController extends Controller
{
    /**
     * Display role permissions.
     */
    /**
     * Display role permissions.
     */
    public function index($roleId)
    {
        $role = Role::findOrFail($roleId);

        // ✅ Get the role with features and permissions
        $roles = Role::with(['features.permissions'])
            ->where('id', $roleId)
            ->get();

        // ✅ Get the role's assigned permission IDs directly
        $assignedPermissionIds = $role->permissions()->pluck('id')->toArray();
        // ✅ Get all module permissions with assignment status
        $module_permission = Modules::whereHas('permissions')
            ->with(['permissions'])
            ->get()
            ->map(function ($module) use ($assignedPermissionIds) {
                return [
                    'id' => $module->id,
                    'name' => $module->name,
                    'slug' => $module->slug,
                    'permissions' => $module->permissions->map(function ($permission) use ($assignedPermissionIds) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                            'slug' => $permission->slug,
                            'assigned' => in_array($permission->id, $assignedPermissionIds),
                        ];
                    }),
                ];
            });

        // ✅ Return the data
        return Inertia::render('UserManagement/User/PermissionRole', [
            'roles' => $roles,
            'module_permission' => $module_permission,
            'role_name' => $role->name,
            'role_id' => $role->id,
            'assigned_permission_ids' => $assignedPermissionIds, // ✅ Pass assigned IDs separately
        ]);
    }

    public function store(Request $request, $roleId)
    {
        // ✅ Log the incoming request for debugging
        \Log::info('Assigning permissions to role', [
            'role_id' => $roleId,
            'modules' => $request->modules,
        ]);

        $validated = $request->validate([
            'modules' => 'required|array',
        ]);

        $role = Role::findOrFail($roleId);

        // Get all permission IDs from the modules
        $permissionIds = collect($validated['modules'])
            ->flatMap(function ($moduleData) {
                return $moduleData['permissions'] ?? [];
            })
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        // Log what's being assigned
        \Log::info('Assigning permission IDs', [
            'role_id' => $roleId,
            'permission_ids' => $permissionIds,
        ]);

        // Sync permissions to the role
        $role->permissions()->sync($permissionIds);

        // Clear permission cache
        app('cache')->forget('spatie.permission.cache');

        return to_route('admin.permissions.role.index', $roleId)
            ->with('success', 'Permissions assigned successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('usermanagement::create');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('usermanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('usermanagement::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Update logic here
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Delete logic here
    }
}