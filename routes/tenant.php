<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\Tenant\ProfileController;
use Illuminate\Foundation\Application;
use Inertia\Inertia;
/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    // Central routes

    Route::get('/', function () {
        return Inertia::render('Tenant/Welcome', [
            'canLogin' => Route::has('tenant.login'),
            'canRegister' => Route::has('tenant.register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    })->name('tenant.welcome');

    Route::get('/dashboard', function () {
        return Inertia::render('Tenant/Dashboard');
    })->middleware(['auth', 'verified'])->name('tenant.dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('tenant.profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('tenant.profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('tenant.profile.destroy');
    });


    // 👇 Auth routes are central-only now
    require __DIR__ . '/tenant-auth.php';
});
