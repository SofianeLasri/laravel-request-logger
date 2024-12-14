<?php

namespace SlProjects\LaravelLib\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use SlProjects\LaravelRequestLogger\app\Http\Middleware\SaveRequestMiddleware;

#[WithMigration]
class SaveRequestMiddlewareTest extends TestCase
{
    use RefreshDatabase, WithWorkbench;

    public function test_it_stores_request_in_cache()
    {
        $response = $this->get('/', [
            'CF-IPCountry' => 'US',
            'User-Agent' => 'TestAgent',
            'referer' => 'http://example.com',
            'origin' => 'http://origin.com',
            'content-type' => 'application/json',
            'content-length' => '123',
        ]);

        $response->assertStatus(200);

        $requests = Cache::get('requests', []);
        $this->assertNotEmpty($requests);

        $storedRequest = $requests[0];
        $this->assertEquals('127.0.0.1', $storedRequest['ip']);
        $this->assertEquals('US', $storedRequest['country_code']);
        $this->assertEquals('http://localhost', $storedRequest['url']);
        $this->assertEquals('GET', $storedRequest['method']);
        $this->assertEquals('TestAgent', $storedRequest['user_agent']);
        $this->assertEquals('http://example.com', $storedRequest['referer']);
        $this->assertEquals('http://origin.com', $storedRequest['origin']);
        $this->assertEquals('application/json', $storedRequest['content_type']);
        $this->assertEquals('123', $storedRequest['content_length']);
        $this->assertEquals(200, $storedRequest['status_code']);
        $this->assertNull($storedRequest['user_id']); // Assuming no user is logged in
    }

    protected function defineRoutes($router): void
    {
        $router->get('/', function () {
            return response()->json(['message' => 'Hello World!']);
        })->middleware(SaveRequestMiddleware::class);
    }
}
