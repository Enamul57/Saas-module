<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Feature as Modules;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\TenantRole as Role;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authUser = auth()->user();

        // ✅ Check for super_admin OR admin (case insensitive)
        $isAdmin = $authUser->hasRole('super_admin') ||
            $authUser->hasRole('admin') ||
            $authUser->hasRole('Admin') ||
            $authUser->hasRole('Super_admin');

        if ($isAdmin) {
            $roles = Role::all();
        } else {
            // For non-admin users, only show their own roles or roles they can manage
            $roles = Role::whereIn('id', $authUser->roles->pluck('id'))->get();
        }

        if ($roles->isEmpty()) {
            // ✅ Use module notation: UserManagement/User/Role
            return Inertia::render('UserManagement/User/Role');
        }

        $modules = Modules::all();
        $users = User::whereNot('id', auth()->id())->get();

        // ✅ Use module notation: UserManagement/User/Role
        return Inertia::render('UserManagement/User/Role', [
            'roles' => $roles,
            'modules' => $modules,
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('usermanagement::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Role::firstOrCreate([
            'name' => strtolower($validated['name']),
            'tenant_id' => session('tenant_id') ?? 1,
        ]);

        return to_route('admin.roles.index')->with('success', 'Role created successfully.');
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
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($id)->where(fn($q) => $q->where('tenant_id', session('tenant_id') ?? 1))
            ],
        ]);

        $role = Role::findById($id);
        $role->update(['name' => strtolower($validated['name'])]);

        return to_route('admin.roles.index')->with('info', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return to_route('admin.roles.index')->with('danger', 'Role deleted successfully.');
    }
}
