<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $host = $request->getHost(); // hrm.example.com or example.com
        Log::info(" host: " . $host);
        $tenant = Company::where('domain', $host)
            ->first();
        Log::info("tenant domain: " . $tenant?->domain);
        if (!$tenant) {
            Log::info('Tenant not found');
            abort(404, 'Tenant not found.');
        }

        // Bind tenant globally
        if ($tenant) {
            app()->instance('tenant', $tenant);
            Session::put('tenant_id', $tenant->id);
            config([
                'app.name' => $tenant->company_name,
                'app.domain' => $tenant->domain,   // important for session cookie
            ]);
            Log::info("tenant id: " . $tenant->id);
        } else {
            Session::forget('tenant_id');
        }

        return $next($request);
    }
}
