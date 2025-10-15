<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Feature as Modules;
use App\Models\Feature;
use App\Models\RolePermissionFeature;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\TenantRole as Role;
use Illuminate\Support\Str;
use App\Models\TenantPermission as Permission;

class ModulesController extends Controller
{
    //
    public function permissonModuleView()
    {

        return Inertia::render('UserManagement/User/Permission');
    }

    public function assignPermissionToModule(Request $request)
    {
        $modules = $request->modules;
        $permissions_collection = collect($request->permissions)->map(fn($permission) => [
            'name' => $permission,
            'slug' => Str::slug(strtolower($permission)) . "-" . $request->modules['slug'],
            'tenant_id' => session('tenant_id'),
        ]);

        $existInDB = Permission::whereIn('slug', $permissions_collection->pluck('slug'))->pluck('slug')->toArray();

        $newPermissions = $permissions_collection->reject(fn($permission) => in_array($permission['slug'], $existInDB))->values()->toArray();
        //dd($newPermissions);
        if (!empty($newPermissions)) {
            foreach ($newPermissions as $permData) {
                Permission::create($permData);
            }
        }

        $permissions_id = Permission::whereIn('slug', $permissions_collection->pluck('slug'))->pluck('id');
        $features = Feature::findOrFail($modules['id']);
        for ($i = 0; $i < count($permissions_id); $i++) {
            $features->permissions()->syncWithoutDetaching($permissions_id->mapWithKeys(fn($id) => [
                $id => ['tenant_id' => session('tenant_id')]
            ])->toArray());
        }
        return to_route('permissions.assign')->with('success', 'Assigned Permission successfuly');
        //check if the permission exists

    }

    public function updatePermissionToModule(Request $request, $oldFeatureId)
    {
        DB::table('feature_permission')->where('feature_id', $oldFeatureId)->where('tenant_id', session('tenant_id'))->delete();
        $collection = $request->all();
        $modules = $request->modules;
        $permissions_collection = collect($request->permissions)->map(fn($permission) => [
            'name' => $permission,
            'slug' => Str::slug(strtolower($permission)) . "-" . $request->modules['slug'],
        ]);

        $existInDB = Permission::whereIn('slug', $permissions_collection->pluck('slug'))->pluck('slug')->toArray();
        $newPermissions = $permissions_collection->reject(fn($permission) => in_array($permission['slug'], $existInDB))->values()->toArray();
        if (!empty($newPermissions)) {
            foreach ($newPermissions as $permData) {
                Permission::create($permData);
            }
        }

        $permissions_id = Permission::whereIn('slug', $permissions_collection->pluck('slug'))->pluck('id');
        $features = Feature::findOrFail($modules['id']);
        for ($i = 0; $i < count($permissions_id); $i++) {
            $features->permissions()->sync($permissions_id->mapWithKeys(fn($id) => [
                $id => ['tenant_id' => session('tenant_id')]
            ])->toArray());
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
        $role->features()->sync(collect($validated['modules'])->mapWithKeys(fn($id) => [
            $id => ['tenant_id' => session('tenant_id')]
        ])->toArray());

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
            $user->roles()->syncWithoutDetaching([
                $role->id => [
                    'tenant_id' => session('tenant_id'),
                ]
            ]);
            $user->update(['role' => $role->name]);
            return to_route('roles.index')->with('info', 'Role assigned to user successfully.');
        }

        return to_route('roles.index')->with('error', 'User or Role not found.');
    }
    //rele permission
    public function assignRolePermission(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);

        $modules = $request->modules;
        $allPermissionIds = collect($modules)
            ->flatten()          // merge all arrays into one
            ->filter(fn($id) => $id > 0) // remove empty / invalid IDs
            ->mapWithKeys(fn($id) => [
                $id => ['tenant_id' => session('tenant_id')]
            ])
            ->toArray();
        $role->permissions()->sync($allPermissionIds);

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
        DB::table('feature_permission')->where('feature_id', $id)->where('tenant_id', session('tenant_id'))->delete();
        return to_route('permissions.assign')->with('danger', 'Deleted Permission successfuly');
    }

    public function fetchModulePermissionJson()
    {
        $modules = Modules::all();
        $module_permission = Modules::whereHas('permissions')->with(['permissions'])->get();
        return response()->json([
            'modules' => $modules,
            'module_permission' => $module_permission,
        ]);
    }
}
