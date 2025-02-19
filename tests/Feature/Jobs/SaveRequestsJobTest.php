<?php

namespace Tests\Feature\Jobs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use PHPUnit\Framework\Attributes\CoversClass;
use SlProjects\LaravelRequestLogger\app\Jobs\SaveRequestsJob;
use Tests\TestCase;

#[CoversClass(SaveRequestsJob::class)]
class SaveRequestsJobTest extends TestCase
{
    use RefreshDatabase, WithWorkbench;

    public function test_it_saves_requests_to_database(): void
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
                'created_at' => now(),
            ],
        ];

        $job = new SaveRequestsJob($requests);
        $job->handle();

        $this->assertDatabaseHas('ip_addresses', ['ip' => '192.168.1.1']);
        $this->assertDatabaseHas('urls', ['url' => 'http://example.com']);
        $this->assertDatabaseHas('user_agents', ['user_agent' => 'TestAgent']);
        $this->assertDatabaseHas('mime_types', ['mime_type' => 'application/json']);
        $this->assertDatabaseHas('logged_requests', [
            'country_code' => 'US',
            'method' => 'GET',
            'content_length' => 123,
            'status_code' => 200,
        ]);
    }

    public function test_it_handles_user_agent_of_various_sizes(): void
    {
        $shortUserAgent = 'ShortAgent';
        $longUserAgent = str_repeat('LongAgent', 100);

        $requests = [
            [
                'ip' => '192.168.1.1',
                'country_code' => 'US',
                'url' => 'http://example.com',
                'method' => 'GET',
                'user_agent' => $shortUserAgent,
                'created_at' => now(),
            ],
            [
                'ip' => '192.168.1.2',
                'country_code' => 'FR',
                'url' => 'http://example.fr',
                'method' => 'POST',
                'user_agent' => $longUserAgent,
                'created_at' => now(),
            ],
        ];

        $job = new SaveRequestsJob($requests);
        $job->handle();

        $this->assertDatabaseHas('user_agents', ['user_agent' => $shortUserAgent]);
        $this->assertDatabaseHas('user_agents', ['user_agent' => $longUserAgent]);
    }
}
