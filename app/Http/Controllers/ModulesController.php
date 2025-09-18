<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Feature as Modules;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ModulesController extends Controller
{
    //
    public function permissonModuleView()
    {
        $modules = Modules::all();
        return Inertia::render('UserManagement/User/Permission', [
            'modules' => $modules
        ]);
    }

    public function assignPermissionToModule(Request $request)
    {
        $collection = $request->all();
        $modules = $request->modules;
        $permissions = $request->permissions;
        //check if the permission exists
        dd($permissions);
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
}
