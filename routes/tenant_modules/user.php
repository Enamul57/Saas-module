<?php

use App\Http\Controllers\ModulesController;
use App\Http\Middleware\HandleInertiaTenantData;
use App\Models\Feature;
use App\Models\RolePermissionFeature;
use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\PermissionController;
use Modules\UserManagement\Http\Controllers\UserController;
use Modules\UserManagement\Http\Controllers\UserManagementController;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

Route::middleware(['auth', 'verified', HandleInertiaTenantData::class])->group(function () {
    //user management routes
    /*create users */
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    /* roles */
    Route::get('/roles', [UserManagementController::class, 'index'])->name('roles.index');
    Route::post('/roles', [UserManagementController::class, 'store'])->name('roles.store');
    // Route::get('/roles/{role}', [UserManagementController::class, 'show'])->name('roles.show');
    // Route::get('/roles/{role}/edit', [UserManagementController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [UserManagementController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [UserManagementController::class, 'destroy'])->name('roles.destroy');
    // roles modules
    Route::get('/permission/modules', [ModulesController::class, 'permissonModuleView'])->name('permissions.assign');
    Route::post('/permission/modules', [ModulesController::class, 'assignPermissionToModule'])->name('permission.module.store');
    Route::put('/permission/modules/{id}', [ModulesController::class, 'updatePermissionToModule'])->name('permission.module.update');
    Route::delete('/permission/module/{id}', [ModulesController::class, 'destroy'])->name('permission.module.delete');
    Route::get('/permission/module/{id}', [ModulesController::class, 'permission_module'])->name('permission_module');

    Route::post('/roles/{role}/modules', [ModulesController::class, 'assignModulesToRole'])->name('roles.modules.assign');
    Route::post('/roles/users/assign', [ModulesController::class, 'assignRoleToUser'])->name('roles.users.assign');
    Route::post('/roles/{role}/permissions', [ModulesController::class, 'assignRolePermission'])->name('role.permission.store');
    Route::get('role/{id}/module', [ModulesController::class, 'role_module'])->name('role_module');
    //set permission
    Route::get('/permission/role/{id}', [PermissionController::class, 'index'])->name('permission.role.index');

    //test permission
    Route::get('/permission', function () {
        // $roleNames = auth()->user()->getRoleNames();
        // $roleIds = Role::whereIn('name', $roleNames)->pluck('id');
        // $moduleRole = Role::whereIn('id', $roleIds)->with('features')->get();
        // $getModuleNames = $moduleRole->pluck('features.*.slug')->flatten();
        // $hasModule = $getModuleNames->contains('user-management');
        // $hasModule = auth()->user()->roles()->with('features')->get()->pluck('features.*.slug')->flatten()->contains('user-management');
        // dd($hasModule);
        //user permisison
        $userPermissions = auth()->user()->roles()
            ->with(['features.permissions'])
            ->get()
            ->pluck('features.*.permissions.*.name') // or id
            ->flatten()
            ->unique();
        dd($userPermissions->toArray());
        $getRolesId = auth()->user()->roles()->pluck('id');
        $getFeatures = Role::whereIn('id', $getRolesId)->with('features')->get()->pluck('features.*.slug')->flatten();
        $featureIds = Feature::whereIn('slug', $getFeatures)->pluck('id');
        $getPermissionThroughFeatureRole = RolePermissionFeature::whereIn('feature_id', $featureIds)->whereIn('role_id', $getRolesId)->get();
        dd($getPermissionThroughFeatureRole->toArray());
    });
});
