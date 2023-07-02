<?php

namespace Bashmohandes7\ZoomService;

use Illuminate\Support\ServiceProvider;

class ZoomServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->publishes([
            __DIR__.'/config/zoomconfig.php' => config_path('zoomconfig.php'),
        ], 'zoomconfig');
    }
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/zoomconfig.php',
            'zoomconfig'
        );
    }
}
