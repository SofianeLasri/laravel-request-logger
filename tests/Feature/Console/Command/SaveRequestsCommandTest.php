<?php

namespace Tests\Feature\Console\Command;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Orchestra\Testbench\Concerns\WithWorkbench;
use PHPUnit\Framework\Attributes\CoversClass;
use SlProjects\LaravelRequestLogger\app\Commands\SaveRequestsCommand;
use SlProjects\LaravelRequestLogger\app\Jobs\SaveRequestsJob;
use Tests\TestCase;

#[CoversClass(SaveRequestsCommand::class)]
class SaveRequestsCommandTest extends TestCase
{
    use RefreshDatabase, WithWorkbench;

    public function test_it_dispatches_save_requests_job()
    {
        $requests = [
            [
                'ip' => '192.168.1.1',
                'country_code' => 'US',
                'url' => 'http://example.com',
                'method' => 'GET',
                'user_agent' => 'TestAgent',
                'referer' => 'http://referer.com',
                'origin' => 'http://origin.com',
                'content_type' => 'application/json',
                'content_length' => 123,
                'status_code' => 200,
                'user_id' => null,
            ],
        ];

        Queue::fake();

        Cache::put('requests', $requests);
        Artisan::call('save:requests');

        Queue::assertPushed(SaveRequestsJob::class, function ($job) use ($requests) {
            return $job->requests === $requests;
        });

        $this->assertEmpty(Cache::get('requests'));
    }
}