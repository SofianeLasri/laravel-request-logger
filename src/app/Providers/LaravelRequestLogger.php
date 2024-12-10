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

        if ($this->app->runningInConsole()) {
            $this->commands([
                SaveRequestsCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->app->singleton(SaveRequestMiddleware::class);
    }
}