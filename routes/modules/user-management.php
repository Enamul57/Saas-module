<?php

use App\Http\Controllers\ModulesController;
use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\PermissionController;
use Modules\UserManagement\Http\Controllers\UserController;
use Modules\UserManagement\Http\Controllers\UserManagementController;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // ==================== USER MANAGEMENT ====================
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])
            ->middleware('permission:view_users')
            ->name('index');

        Route::get('/create', [UserController::class, 'create'])
            ->middleware('permission:create_users')
            ->name('create');

        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:create_users')
            ->name('store');

        Route::get('/search', [UserController::class, 'search'])
            ->middleware('permission:view_users')
            ->name('search');

        Route::get('/{user}', [UserController::class, 'show'])
            ->middleware('permission:view_users')
            ->name('show');

        Route::get('/{user}/edit', [UserController::class, 'edit'])
            ->middleware('permission:edit_users')
            ->name('edit');

        Route::put('/{user}', [UserController::class, 'update'])
            ->middleware('permission:edit_users')
            ->name('update');

        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:delete_users')
            ->name('destroy');

        Route::post('/{user}/assign-role', [ModulesController::class, 'assignRoleToUser'])
            ->middleware('permission:assign_roles')
            ->name('assign-role');
    });

    // ==================== ROLE MANAGEMENT ====================
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])
            ->middleware('permission:view_roles')
            ->name('index');

        Route::post('/', [UserManagementController::class, 'store'])
            ->middleware('permission:create_roles')
            ->name('store');

        Route::get('/{role}', [UserManagementController::class, 'show'])
            ->middleware('permission:view_roles')
            ->name('show');

        Route::get('/{role}/edit', [UserManagementController::class, 'edit'])
            ->middleware('permission:edit_roles')
            ->name('edit');

        Route::put('/{role}', [UserManagementController::class, 'update'])
            ->middleware('permission:edit_roles')
            ->name('update');

        Route::delete('/{role}', [UserManagementController::class, 'destroy'])
            ->middleware('permission:delete_roles')
            ->name('destroy');

        Route::post('/{role}/modules', [ModulesController::class, 'assignModulesToRole'])
            ->middleware('permission:assign_permissions_to_roles')
            ->name('modules.assign');

        Route::post('/{role}/permissions', [ModulesController::class, 'assignRolePermission'])
            ->middleware('permission:assign_permissions_to_roles')
            ->name('permissions.store');
    });

    // ==================== PERMISSION MANAGEMENT ====================
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/modules', [ModulesController::class, 'permissonModuleView'])
            ->middleware('permission:view_permissions')
            ->name('assign');

        Route::get('/modules/fetchJson', [ModulesController::class, 'fetchModulePermissionJson'])
            ->middleware('permission:view_permissions')
            ->name('module.fetch');

        Route::post('/modules', [ModulesController::class, 'assignPermissionToModule'])
            ->middleware('permission:assign_permissions')
            ->name('module.store');

        Route::put('/modules/{id}', [ModulesController::class, 'updatePermissionToModule'])
            ->middleware('permission:edit_permissions')
            ->name('module.update');

        Route::delete('/module/{id}', [ModulesController::class, 'destroy'])
            ->middleware('permission:delete_permissions')
            ->name('module.delete');

        Route::get('/role/{id}', [PermissionController::class, 'index'])
            ->middleware('permission:view_permissions')
            ->name('role.index');

        Route::post('/role/{role}', [PermissionController::class, 'store'])
            ->middleware('permission:assign_permissions')
            ->name('role.store');
    });
});