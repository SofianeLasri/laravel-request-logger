# Laravel Request Logger

[![Latest Version](https://img.shields.io/github/v/release/SofianeLasri/laravel-request-logger)](https://github.com/SofianeLasri/laravel-request-logger/releases)
[![License](https://img.shields.io/github/license/SofianeLasri/laravel-request-logger)](LICENSE)
[![codecov](https://codecov.io/gh/SofianeLasri/laravel-request-logger/graph/badge.svg?token=Y2BKM3ZMFV)](https://codecov.io/gh/SofianeLasri/laravel-request-logger)
[![PHP Version](https://img.shields.io/packagist/php-v/sl-projects/laravel-request-logger)](https://packagist.org/packages/sl-projects/laravel-request-logger)
[![Laravel Version](https://img.shields.io/badge/Laravel-11.9%2B-orange)](https://laravel.com)

A high-performance Laravel package for logging and analyzing HTTP requests with minimal overhead. Built with a cache-first approach and normalized database structure for efficient storage and querying.

**NOTE:** This package has no connection with the Laravel framework or its creators. It is an independent project developed by [Sofiane Lasri](https://sofianelasri.fr), mainly for educational purposes.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
  - [For Laravel 11+ (New Structure)](#for-laravel-11-new-structure)
  - [For Laravel 10 and Earlier](#for-laravel-10-and-earlier)
- [Usage](#usage)
- [Advanced Features](#advanced-features)
- [Database Schema](#database-schema)
- [Performance Considerations](#performance-considerations)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## Features

- 🚀 **High Performance**: Cache-first approach ensures zero impact on request processing
- 📊 **Normalized Database**: Efficient storage with deduplicated entities (IPs, URLs, User Agents, MIME types)
- 🔄 **Async Processing**: Background job processing for database persistence
- 📈 **Rich Data Capture**: Logs IP addresses, HTTP methods, status codes, response times, and more
- 🌍 **GeoIP Support**: Automatic country code detection from IP addresses
- 🛠️ **Fully Configurable**: Customize cache TTL, storage keys, and processing behavior
- 🧪 **100% Tested**: Comprehensive test coverage with factories for all models
- 🔒 **Thread-Safe**: Cache locking prevents race conditions in high-traffic scenarios

## Requirements

- PHP 8.2 or higher
- Laravel 11.9+ (with support for Laravel 12)
- Redis or Memcached recommended for caching (fallback to file cache supported)

## Installation

Install the package via Composer:

```bash
composer require sl-projects/laravel-request-logger
```

Run the migrations to create the necessary database tables:

```bash
php artisan migrate
```

(Optional) Publish the configuration file for customization:

```bash
php artisan vendor:publish --tag=request-logger-config
```

## Configuration

### For Laravel 11+ (New Structure)

Laravel 11 introduced a new application structure with simplified configuration. Here's how to set up the package:

#### 1. Register Middleware

In `bootstrap/app.php`:

```php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use SlProjects\LaravelRequestLogger\app\Http\Middleware\SaveRequestMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware (logs all requests)
        $middleware->append(SaveRequestMiddleware::class);
        
        // OR for specific routes only
        $middleware->appendToGroup('web', SaveRequestMiddleware::class);
        
        // OR create an alias for selective use
        $middleware->alias([
            'log.request' => SaveRequestMiddleware::class,
        ]);
    })
    ->create();
```

#### 2. Schedule the Command

In `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('save:requests')->everyMinute();
```

### For Laravel 10 and Earlier

For Laravel versions 10 and below, use the traditional configuration approach:

#### 1. Register Middleware

In `app/Http/Kernel.php`:

```php
namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use SlProjects\LaravelRequestLogger\app\Http\Middleware\SaveRequestMiddleware;

class Kernel extends HttpKernel
{
    // Global middleware (logs all requests)
    protected $middleware = [
        // ... other middleware
        SaveRequestMiddleware::class,
    ];
    
    // OR for web routes only
    protected $middlewareGroups = [
        'web' => [
            // ... other middleware
            SaveRequestMiddleware::class,
        ],
    ];
    
    // OR create an alias for selective use
    protected $middlewareAliases = [
        // ... other aliases
        'log.request' => SaveRequestMiddleware::class,
    ];
}
```

#### 2. Schedule the Command

In `app/Console/Kernel.php`:

```php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('save:requests')->everyMinute();
    }
}
```

## Usage

### Basic Usage

Once configured, the package automatically logs all incoming requests. The middleware captures request data in the `terminate()` method to ensure zero impact on response times.

### Selective Logging

Apply middleware to specific routes only:

```php
// Laravel 11+ (routes/web.php)
Route::middleware(['log.request'])->group(function () {
    Route::get('/api/users', [UserController::class, 'index']);
    Route::post('/api/users', [UserController::class, 'store']);
});

// Or for individual routes
Route::get('/dashboard', DashboardController::class)
    ->middleware('log.request');
```

### Manual Processing

Process cached requests manually without waiting for the scheduler:

```bash
php artisan save:requests
```

### Querying Logged Requests

```php
use SlProjects\LaravelRequestLogger\app\Models\LoggedRequest;

// Get all requests from a specific IP
$requests = LoggedRequest::with(['ipAddress', 'url', 'userAgent'])
    ->whereHas('ipAddress', function ($query) {
        $query->where('ip', '192.168.1.1');
    })
    ->get();

// Get requests by status code
$errors = LoggedRequest::where('status_code', '>=', 400)
    ->where('status_code', '<', 500)
    ->get();

// Get requests by URL pattern
$apiRequests = LoggedRequest::whereHas('url', function ($query) {
    $query->where('url', 'like', '/api/%');
})->get();

// Analyze request patterns
$topUserAgents = LoggedRequest::with('userAgent')
    ->select('user_agent_id', DB::raw('count(*) as total'))
    ->groupBy('user_agent_id')
    ->orderByDesc('total')
    ->limit(10)
    ->get();
```

## Advanced Features

### Configuration Options

After publishing the config file, you can customize:

```php
// config/request-logger.php
return [
    'cache' => [
        // Cache key for storing requests
        'key' => env('REQUEST_LOGGER_CACHE_KEY', 'logged_requests'),
        
        // Cache TTL in seconds (default: 2 hours)
        'ttl' => env('REQUEST_LOGGER_CACHE_TTL', 7200),
        
        // Lock timeout for cache operations
        'lock_timeout' => env('REQUEST_LOGGER_LOCK_TIMEOUT', 10),
    ],
    
    'processing' => [
        // Batch size for database inserts
        'batch_size' => env('REQUEST_LOGGER_BATCH_SIZE', 100),
        
        // Enable/disable async processing
        'async' => env('REQUEST_LOGGER_ASYNC', true),
    ],
    
    'logging' => [
        // Fields to exclude from logging
        'exclude_fields' => [
            'password',
            'password_confirmation',
            'credit_card',
        ],
        
        // URLs to exclude from logging
        'exclude_urls' => [
            'telescope/*',
            'horizon/*',
            '_debugbar/*',
        ],
    ],
];
```

### Custom Processing

Create custom processors for specific request types:

```php
use SlProjects\LaravelRequestLogger\app\Jobs\SaveRequestsJob;

class CustomRequestProcessor extends SaveRequestsJob
{
    public function handle(): void
    {
        // Custom processing logic
        parent::handle();
        
        // Additional processing
        $this->notifyAdminOfHighTraffic();
        $this->detectAnomalies();
    }
}
```

### Event Listeners

The package dispatches events you can listen to:

```php
// App\Providers\EventServiceProvider
protected $listen = [
    \SlProjects\LaravelRequestLogger\Events\RequestsProcessed::class => [
        \App\Listeners\AnalyzeRequestPatterns::class,
        \App\Listeners\SendTrafficReport::class,
    ],
];
```

## Database Schema

The package uses a normalized database structure for optimal performance:

### Tables

- **`logged_requests`**: Main table containing request records with foreign keys
- **`ip_addresses`**: Deduplicated IP addresses with country codes
- **`user_agents`**: Deduplicated user agent strings
- **`mime_types`**: Deduplicated MIME types
- **`urls`**: Deduplicated URL paths

### Indexes

All foreign key columns and frequently queried fields are properly indexed for optimal query performance.

## Performance Considerations

### Cache Strategy

- Requests are initially stored in cache to avoid blocking the response
- Background job processes cached requests in batches
- Model ID caching prevents repeated database lookups

### Optimization Tips

1. **Use Redis/Memcached**: File-based cache can be slow under high load
2. **Adjust Batch Size**: Larger batches reduce database calls but use more memory
3. **Schedule Frequency**: Run `save:requests` more frequently under high traffic
4. **Database Indexes**: Ensure all foreign keys are properly indexed
5. **Pruning Old Data**: Regularly clean old records to maintain performance

```php
// Example: Prune records older than 30 days
LoggedRequest::where('created_at', '<', now()->subDays(30))->delete();
```

## Testing

Run the test suite:

```bash
composer test
```

Run static analysis:

```bash
vendor/bin/phpstan analyse
```

The package includes comprehensive tests for:
- Middleware functionality
- Job processing
- Model relationships and factories
- Command execution
- Cache operations

## Contributing

Contributions are welcome! Since this is my first package, I would appreciate any feedback, suggestions, or improvements you can provide.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

Please ensure all tests pass and add tests for new features.

## License

This package is open-source software licensed under the [MIT license](LICENSE).

## Author

Developed by [Sofiane Lasri](https://sofianelasri.fr).

For any inquiries or suggestions, feel free to create an issue on [GitHub](https://github.com/SofianeLasri/laravel-request-logger/issues).