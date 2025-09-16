<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


//checking web routes
foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)
        ->middleware(['web']) // ensure session/auth/web middlewares apply
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

            Route::middleware('auth')->group(function () {
                // Route::get('/profile', [ProfileController::class, 'edit'])->name('central.profile.edit');
                // Route::patch('/profile', [ProfileController::class, 'update'])->name('central.profile.update');
                // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('central.profile.destroy');

                //create tenant 
                Route::resource('company', TenantController::class);
            });


            // 👇 Auth routes are central-only now
            require __DIR__ . '/auth.php';
        });
}
