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
     * Display a listing of the resource.
     */
    public function index($roleId)
    {
        $role = Role::findOrFail($roleId);
        $roles = Role::whereHas('features', function ($query) use ($roleId) {
            $query->where('role_id', $roleId);
        })->with('features.permissions')->get();

        $module_permission = Modules::whereHas('permissions')->with(['permissions'])->get();

        return Inertia::render('UserManagement/User/PermissionRole', [
            'roles' => $roles,
            'module_permission' => $module_permission,
            'role_name' => $role->name,
            'role_id' => $role->id,
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
    public function store(Request $request) {}

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
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
