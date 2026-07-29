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

        // ✅ SEARCH ROUTE MUST COME BEFORE THE {user} ROUTE
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

        // Assign role to user
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

        // Assign modules to role
        Route::post('/{role}/modules', [ModulesController::class, 'assignModulesToRole'])
            ->middleware('permission:assign_permissions_to_roles')
            ->name('modules.assign');

        // Assign permissions to role
        Route::post('/{role}/permissions', [ModulesController::class, 'assignRolePermission'])
            ->middleware('permission:assign_permissions_to_roles')
            ->name('permissions.store');
    });

    // ==================== PERMISSION MANAGEMENT ====================
    Route::prefix('permissions')->name('permissions.')->group(function () {
        // Module permissions view
        Route::get('/modules', [ModulesController::class, 'permissonModuleView'])
            ->middleware('permission:view_permissions')
            ->name('assign');

        // Fetch module permissions JSON
        Route::get('/modules/fetchJson', [ModulesController::class, 'fetchModulePermissionJson'])
            ->middleware('permission:view_permissions')
            ->name('module.fetch');

        // Assign permission to module
        Route::post('/modules', [ModulesController::class, 'assignPermissionToModule'])
            ->middleware('permission:assign_permissions')
            ->name('module.store');

        // Update permission to module
        Route::put('/modules/{id}', [ModulesController::class, 'updatePermissionToModule'])
            ->middleware('permission:edit_permissions')
            ->name('module.update');

        // Delete module permission
        Route::delete('/module/{id}', [ModulesController::class, 'destroy'])
            ->middleware('permission:delete_permissions')
            ->name('module.delete');

        // Role permissions view - uses PermissionController
        Route::get('/role/{id}', [PermissionController::class, 'index'])
            ->middleware('permission:view_permissions')
            ->name('role.index');

        // Store role permissions
        Route::post('/role/{role}', [PermissionController::class, 'store'])
            ->middleware('permission:assign_permissions')
            ->name('role.store');
    });
});
