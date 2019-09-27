<?php

namespace Spatie\ResourceLinks;

use Illuminate\Support\ServiceProvider;

class ResourceLinksServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/resource-links.php' => config_path('resource-links.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/resource-links.php', 'resource-links');
    }
}
