<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
        // 👉 Central routes
        // Route::middleware('web')
        //     ->group(function () {
        //         $this->mapModuleRoutes();
        //     });

        // // 👉 Tenant routes
        // Route::middleware(['web', 'tenant.auth'])
        //     ->domain('{tenant}.localhost:8000')
        //     ->group(function () {
        //         $this->tenantMapModuleRoutes();
        //     });
    }

    // public function mapModuleRoutes()
    // {
    //     $modulesPath = base_path('routes/modules');
    //     foreach (glob($modulesPath . "/*php") as $modulePath) {
    //         require $modulePath;
    //     }
    // }

    // public function tenantMapModuleRoutes()
    // {
    //     $modulesPath = base_path('routes/tenant_modules');
    //     foreach (glob($modulesPath . "/*php") as $modulePath) {
    //         require $modulePath;
    //     }
    // }
}
