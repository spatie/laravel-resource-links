<?php

namespace Spatie\LaravelResourceEndpoints;

use Illuminate\Support\ServiceProvider;

class ResourceEndpointsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/resource-endpoints.php' => config_path('resource-endpoints.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/resource-endpoints.php', 'resource-endpoints');
    }
}
