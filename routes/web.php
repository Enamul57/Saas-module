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
//checking web routes

Route::middleware(['web', IdentifyTenant::class]) // ensure session/auth/web middlewares apply
    ->group(function () {
        // Central routes

        Route::get('/', function () {
            return Inertia::render('Welcome', [
                'canLogin' => Route::has('central.login'),
                'canRegister' => Route::has('central.register'),
                'laravelVersion' => Application::VERSION,
                'phpVersion' => PHP_VERSION,
            ]);
        });
        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->middleware(['auth', 'verified'])->name('central.dashboard');

        Route::get('/tests', function () {
            $domain = config('app.domain');
            $parts = explode('.', $domain);
            $split = count($parts) > 2 ? 'ok.' . implode('.', array_slice($parts, 1)) : 'ok.' . implode('.', $parts);
            dd($split);
            $session_domain = explode('.', config('app.domain'))[1];
            dd($session_domain);
        });
        Route::middleware(['auth'])->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            // Route::patch('/profile', [ProfileController::class, 'update'])->name('central.profile.update');
            // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('central.profile.destroy');

            //create tenant 
            Route::resource('company', TenantController::class);
            Route::get('/test', function () {
                $user = Auth::user();
                $roles = $user->getRoleNames();
                //get all permissions
                $permissions = $user->getAllPermissions();
                //get permssion through roles

                dd($permissions->toArray());
            });
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
        require __DIR__ . '/auth.php';
    });
