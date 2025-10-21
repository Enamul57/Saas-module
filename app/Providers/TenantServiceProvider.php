<?php

namespace App\Providers;

use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class TenantServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->singleton('tenant', function ($app) {
            $tenant_id = session('tenant_id');
            if ($tenant_id) {
                return Company::findOrFail($tenant_id);
            }
            $host = request()->getHost();
            Log::info('request host ' . $host);
            $company = Company::where('company', $host)->first();
            Log::info('app service provider ' . $company);
            return $company;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
