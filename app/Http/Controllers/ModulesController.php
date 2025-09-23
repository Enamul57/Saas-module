<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Feature as Modules;
use App\Models\Feature;
use App\Models\RolePermissionFeature;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class ModulesController extends Controller
{
    //
    public function permissonModuleView()
    {
        $modules = Modules::all();
        $module_permission = Modules::whereHas('permissions')->with(['permissions'])->get();
        return Inertia::render('UserManagement/User/Permission', [
            'modules' => $modules,
            'module_permission' => $module_permission,
        ]);
    }

    public function assignPermissionToModule(Request $request)
    {
        $collection = $request->all();
        $modules = $request->modules;
        $permissions_collection = collect($request->permissions)->map(fn($permission) => [
            'name' => $permission,
            'slug' => Str::slug(strtolower($permission)),
        ]);
        $existInDB = DB::table('permissions')->whereIn('slug', $permissions_collection->pluck('slug'))->pluck('slug')->toArray();
        $newPermissions = $permissions_collection->reject(fn($permission) => in_array($permission['slug'], $existInDB))->values()->toArray();
        if (!empty($newPermissions)) {
            DB::table('permissions')->insert($newPermissions);
        }

        $permissions_id = DB::table('permissions')->whereIn('slug', $permissions_collection->pluck('slug'))->pluck('id');
        $features = Feature::findOrFail($modules['id']);
        for ($i = 0; $i < count($permissions_id); $i++) {
            $features->permissions()->syncWithOutDetaching($permissions_id);
        }
        return to_route('permissions.assign')->with('success', 'Assigned Permission successfuly');
        //check if the permission exists

    }

    public function updatePermissionToModule(Request $request, $oldFeatureId)
    {
        DB::table('feature_permission')->where('feature_id', $oldFeatureId)->delete();
        $collection = $request->all();
        $modules = $request->modules;
        $permissions_collection = collect($request->permissions)->map(fn($permission) => [
            'name' => $permission,
            'slug' => Str::slug(strtolower($permission)),
        ]);
        $existInDB = DB::table('permissions')->whereIn('slug', $permissions_collection->pluck('slug'))->pluck('slug')->toArray();
        $newPermissions = $permissions_collection->reject(fn($permission) => in_array($permission['slug'], $existInDB))->values()->toArray();
        if (!empty($newPermissions)) {
            DB::table('permissions')->insert($newPermissions);
        }

        $permissions_id = DB::table('permissions')->whereIn('slug', $permissions_collection->pluck('slug'))->pluck('id');
        $features = Feature::findOrFail($modules['id']);
        for ($i = 0; $i < count($permissions_id); $i++) {
            $features->permissions()->syncWithOutDetaching($permissions_id);
        }
        return to_route('permissions.assign')->with('info', 'Updated Permission successfuly');
    }
    public function create()
    {
        $modules = Modules::all();
        return Inertia::render('UserManagement/User/Permission', [
            'modules' => $modules
        ]);
    }

    public function assignModulesToRole(Request $request, $roleId)
    {
        $validated = $request->validate([
            'modules' => ['required', 'array'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $role = \Spatie\Permission\Models\Role::findById($roleId);
        $role->features()->sync($validated['modules']);

        return to_route('roles.index')->with('success', 'Modules assigned to role successfully.');
    }

    public function assignRoleToUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $user = User::findOrFail($validated['user_id']);
        $role = Role::findById($validated['role_id']);

        if ($user && $role) {
            $user->syncRoles([$role->name]);
            return to_route('roles.index')->with('info', 'Role assigned to user successfully.');
        }

        return to_route('roles.index')->with('error', 'User or Role not found.');
    }
    //rele permission
    public function assignRolePermission(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);

        $modules = $request->modules;
        foreach ($modules as $featureId => $permissionIds) {
            if (count($permissionIds) > 0) {
                foreach ($permissionIds as $permissionId) {
                    RolePermissionFeature::updateOrInsert([
                        'role_id' => $roleId,
                        'feature_id' => $featureId,
                        'permission_id' => $permissionId,
                    ], [
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        return to_route('roles.index')->with('success', 'Assigned Permissions to ' . $role->name);
    }

    public function role_module($role)
    {
        $role_module = Role::where('id', $role)->with(['features'])->get();
        return response()->json($role_module);
    }
    public function permission_module($moduleId)
    {
        $modulePermission = Modules::where('id', $moduleId)->with('permissions')->get();
        return response()->json($modulePermission);
    }
    public function destroy($id)
    {
        DB::table('feature_permission')->where('feature_id', $id)->delete();
        return to_route('permissions.assign')->with('danger', 'Deleted Permission successfuly');
    }
}
