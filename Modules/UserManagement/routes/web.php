<?php

use App\Http\Controllers\ModulesController;
use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\PermissionController;
use Modules\UserManagement\Http\Controllers\UserController;
use Modules\UserManagement\Http\Controllers\UserManagementController;

Route::middleware(['auth', 'verified'])->group(function () {
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
    Route::get('/permission/modules/fetchJson', [ModulesController::class, 'fetchModulePermissionJson'])->name('permissions.module.fetch');
    Route::post('/permission/modules', [ModulesController::class, 'assignPermissionToModule'])->name('permission.module.store');
    Route::put('/permission/modules/{id}', [ModulesController::class, 'updatePermissionToModule'])->name('permission.module.update');
    Route::delete('/permission/module/{id}', [ModulesController::class, 'destroy'])->name('permission.module.delete');
    Route::post('/roles/{role}/modules', [ModulesController::class, 'assignModulesToRole'])->name('roles.modules.assign');
    Route::post('/roles/users/assign', [ModulesController::class, 'assignRoleToUser'])->name('roles.users.assign');
    Route::post('/roles/{role}/permissions', [ModulesController::class, 'assignRolePermission'])->name('role.permission.store');
    //set permission
    Route::get('/permission/role/{id}', [PermissionController::class, 'index'])->name('permission.role.index');
});
