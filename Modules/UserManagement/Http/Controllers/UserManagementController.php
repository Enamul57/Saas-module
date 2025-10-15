<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Feature as Modules;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PhpParser\Node\Expr\AssignOp\Mod;
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
        if ($authUser->hasRole('Admin')) {
            $roles = Role::all();
        } else {
            $roles = Role::whereIn('id', $authUser->roles->pluck('id'))->get();
        }
        if ($roles->isEmpty()) {
            return Inertia::render('UserManagement/User/Role');
        }
        $modules = Modules::all();
        $users = User::whereNot('id', auth()->id())->get();
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

        Role::firstOrCreate(['name' => strtolower($validated['name'])]);

        return to_route('roles.index')->with('success', 'Role created successfully.');
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
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($id)->where(fn($q) => $q->where('tenant_id', session('tenant_id')))],
        ]);
        $role = Role::findById($id);

        $role->update(['name' => $validated['name']]);

        return to_route('roles.index')->with('info', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return to_route('roles.index')->with('danger', 'Role deleted successfully.');
    }
}
