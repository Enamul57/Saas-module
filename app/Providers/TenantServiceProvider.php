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
            // if ($tenant_id) {
            //     return Company::findOrFail($tenant_id);
            // }
            $host = request()->getHost();
            Log::info('request host ' . $host);
            $domain = Company::where('domain', $host)->first();
            Log::info('app service provider ' . $domain);
            return $domain;
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
