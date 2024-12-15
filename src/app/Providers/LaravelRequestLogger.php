<?php

namespace SlProjects\LaravelRequestLogger\app\Providers;

use Illuminate\Support\ServiceProvider;
use SlProjects\LaravelRequestLogger\app\Commands\SaveRequestsCommand;
use SlProjects\LaravelRequestLogger\app\Http\Middleware\SaveRequestMiddleware;

class LaravelRequestLogger extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->publishes([
            __DIR__ . '/../../config/request-logger.php' => config_path('request-logger.php'),
        ], 'request-logger-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                SaveRequestsCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->app->singleton(SaveRequestMiddleware::class);
        $this->mergeConfigFrom(__DIR__ . '/../../config/request-logger.php', 'request-logger');
    }
}