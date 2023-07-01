<?php

namespace Bashmohandes7\Zoom;

use Illuminate\Support\ServiceProvider;

class ZoomServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
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