# Laravel Request Logger

[![Latest Version](https://img.shields.io/github/v/release/SofianeLasri/laravel-request-logger)](https://github.com/SofianeLasri/laravel-request-logger/releases)
[![License](https://img.shields.io/github/license/SofianeLasri/laravel-request-logger)](LICENSE)

Laravel Request Logger is a package designed to log all incoming HTTP requests in a Laravel application. It provides
middleware to capture request details and store them efficiently using caching and database storage.

**NOTE:** This package has no connection with the Laravel framework or its creators. It is an independent project
developed by [Sofiane Lasri](https://sofianelasri.fr), mainly for educational purposes.

## Features

- Logs IP addresses, country codes, HTTP methods, content lengths, status codes, user agents, MIME types, URLs,
  referers, and origins.
- Uses caching for efficient database operations.
- Provides a command to persist logged requests from cache to the database.
- Includes factories for testing and seeding purposes.
- Supports Laravel 11.9+ and PHP 8.2+ (but may work with older versions).

## Installation

You can install the package via Composer:

```bash
composer require sl-projects/laravel-request-logger
```

After installing, the package will automatically register its service provider.

## Configuration

1. **Migrations**: Run the migrations to create the necessary database tables.

    ```bash
    php artisan migrate
    ```

2. **Middleware**: Add the `SaveRequestMiddleware` to your HTTP kernel or specific routes you want to log.

    ```php
    // In app/Http/Kernel.php (for "old" Laravel 11 and older)
    protected $middleware = [
        // ...
        \SlProjects\LaravelRequestLogger\app\Http\Middleware\SaveRequestMiddleware::class,
    ];
   
    // In bootstrap/app.php (for "new" Laravel 11 and newer)
    return Application::configure(basePath: dirname(__DIR__))
        ->withMiddleware(function (Middleware $middleware) {
            // ...
            $middleware->append(\SlProjects\LaravelRequestLogger\app\Http\Middleware\SaveRequestMiddleware::class);
        });
    ```
   You can off course add the middleware to specific routes only, and use imports.


3. **Scheduler**: Add the `SaveRequests` command to the Laravel scheduler to persist the cached requests to the
   database.

    ```php
    // In app/Console/Kernel.php (for "old" Laravel 11 and older)
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('save:requests')->everyMinute();
    }
   
    // In routes/console.php (for "new" Laravel 11 and newer)
    Schedule::command('save:requests')->everyMinute();
    ```

## Usage

### Logging Requests

The middleware automatically logs requests to the cache. You can customize which routes to log by applying the
middleware selectively.

### Saving Requests

To persist the cached requests to the database, use the provided Artisan command:

```bash
php artisan save:requests
```

This command will dispatch a job to save the requests asynchronously.

## Contributing

Contributions are welcome! Since it is my first package, I would appreciate any feedback, suggestions, or improvements
you can provide.

## License

This package is open-source software licensed under the [MIT license](LICENSE).

## Author

Developed by [Sofiane Lasri](https://sofianelasri.fr).

For any inquiries or suggestions, feel free to create an issue.