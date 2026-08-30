<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();

        // Debug: Log the user data
        if ($user) {
            \Log::info('User Features:', ['features' => $user->getAllFeatures()]);
            \Log::info('User Permissions:', ['permissions' => $user->getAllPermissionsFromRoles()]);
            \Log::info('User Roles:', ['roles' => $user->getRoleNames()->toArray()]);
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? $user->getAuthData() : null,
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
                'warning' => fn() => $request->session()->get('warning'),
                'info' => fn() => $request->session()->get('info'),
            ],
        ]);
    }
}