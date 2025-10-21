<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        // Collection::macro('toUcFirst', function ($field = 'key', $valueField = 'value') {
        //     return $this->mapWithKeys(function ($item) use ($field, $valueField) {
        //         $formatedKey = ucfirst(str_replace('_', ' ', $item[$field]));
        //         return [$formatedKey => $item[$valueField], $item['group'] => $item['group']];
        //     });
        // });
    }
}
