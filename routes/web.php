<?php

use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\ModulesController;
use App\Http\Middleware\HandleInertiaTenantData;
use App\Http\Middleware\IdentifyTenant;
use App\Models\Feature;
use App\Models\RolePermissionFeature;
use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\PermissionController;
use Modules\UserManagement\Http\Controllers\UserController;
use Modules\UserManagement\Http\Controllers\UserManagementController;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Main web routes with tenant identification
Route::middleware(['web', IdentifyTenant::class])->group(function () {

    // Public routes (no auth required)
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('central.login'),
            'canRegister' => Route::has('central.register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    });

    // Auth routes (no auth required)
    require __DIR__ . '/auth.php';

    // Authenticated routes with permission checks
    Route::middleware(['auth', 'verified'])->group(function () {

        // Dashboard - Only users with view_dashboard permission
        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })
            ->middleware('permission:view_dashboard')
            ->name('central.dashboard');

        // Profile routes - Self only
        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');

        // Tenant management - Admin only
        Route::resource('company', TenantController::class)
            ->middleware('permission:manage_tenants');

        // Test routes (remove in production)
        Route::get('/test', function () {
            $user = Auth::user();
            $roles = $user->getRoleNames();
            $permissions = $user->getAllPermissions();
            dd($roles->toArray());
        });

        Route::get('/permission', function () {
            dd(auth()->user()->roles()->with(['permissions'])->get()->toArray());
        });
    });
});
Route::get('/debug-auth', function () {
    $user = auth()->user();
    if (!$user) {
        return ['message' => 'Not logged in'];
    }

    return [
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'roles' => $user->getRoleNames()->toArray(),
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
        ]
    ];
});
// Load module routes separately
require __DIR__ . '/modules/pim.php';
// require __DIR__ . '/modules/user-management.php';
// require __DIR__ . '/modules/attendance.php';