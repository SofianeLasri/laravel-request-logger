<?php

namespace SlProjects\LaravelRequestLogger\app\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use SlProjects\LaravelRequestLogger\app\Jobs\SaveRequestsJob;

class SaveRequestsCommand extends Command
{
    protected $signature = 'save:requests';

    protected $description = 'Save pending requests to database';

    public function handle(): void
    {
        $cacheKey = config('request-logger.cache_key');
        $requests = Cache::get($cacheKey, []);
        if (!empty($requests)) {
            $this->info('Saving ' . count($requests) . ' requests');
            Cache::forget($cacheKey);
            SaveRequestsJob::dispatch($requests);
        }
    }
}
