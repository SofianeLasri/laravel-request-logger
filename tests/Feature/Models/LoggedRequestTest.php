<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use SlProjects\LaravelRequestLogger\app\Models\IpAddress;
use SlProjects\LaravelRequestLogger\app\Models\LoggedRequest;
use SlProjects\LaravelRequestLogger\app\Models\MimeType;
use SlProjects\LaravelRequestLogger\app\Models\Url;
use SlProjects\LaravelRequestLogger\app\Models\UserAgent;
use Tests\TestCase;

#[CoversClass(LoggedRequest::class)]
class LoggedRequestTest extends TestCase
{
    use RefreshDatabase, WithWorkbench;

    #[Test]
    public function testCreateInstance(): void
    {
        $loggedRequest = LoggedRequest::factory()->create();

        $this->assertDatabaseHas('logged_requests', [
            'id' => $loggedRequest->id,
            'ip_address_id' => $loggedRequest->ip_address_id,
            'country_code' => $loggedRequest->country_code,
            'method' => $loggedRequest->method,
            'content_length' => $loggedRequest->content_length,
            'status_code' => $loggedRequest->status_code,
            'user_agent_id' => $loggedRequest->user_agent_id,
            'mime_type_id' => $loggedRequest->mime_type_id,
            'url_id' => $loggedRequest->url_id,
            'referer_url_id' => $loggedRequest->referer_url_id,
            'origin_url_id' => $loggedRequest->origin_url_id,
        ]);
    }
    #[Test]
    public function testCreateInstanceWithAllRelations():void
    {
        $ipAddress = IpAddress::factory()->create();
        $userAgent = UserAgent::factory()->create();
        $mimeType = MimeType::factory()->create();
        $url = Url::factory()->create();
        $refererUrl = Url::factory()->create();
        $originUrl = Url::factory()->create();

        $loggedRequest = LoggedRequest::factory()->create([
            'ip_address_id' => $ipAddress->id,
            'country_code' => 'US',
            'method' => 'GET',
            'content_length' => 1234,
            'status_code' => 200,
            'user_agent_id' => $userAgent->id,
            'mime_type_id' => $mimeType->id,
            'url_id' => $url->id,
            'referer_url_id' => $refererUrl->id,
            'origin_url_id' => $originUrl->id,
        ]);

        $this->assertDatabaseHas('logged_requests', [
            'id' => $loggedRequest->id,
            'ip_address_id' => $ipAddress->id,
            'country_code' => 'US',
            'method' => 'GET',
            'content_length' => 1234,
            'status_code' => 200,
            'user_agent_id' => $userAgent->id,
            'mime_type_id' => $mimeType->id,
            'url_id' => $url->id,
            'referer_url_id' => $refererUrl->id,
            'origin_url_id' => $originUrl->id,
        ]);
    }

    #[Test]
    public function testCreateInstanceWithOnlyRequiredRelations():void
    {
        $ipAddress = IpAddress::factory()->create();

        $loggedRequest = LoggedRequest::factory()->create([
            'ip_address_id' => $ipAddress->id,
            'country_code' => null,
            'method' => 'POST',
            'content_length' => null,
            'status_code' => null,
            'user_agent_id' => null,
            'mime_type_id' => null,
            'url_id' => null,
            'referer_url_id' => null,
            'origin_url_id' => null,
        ]);

        $this->assertDatabaseHas('logged_requests', [
            'id' => $loggedRequest->id,
            'ip_address_id' => $ipAddress->id,
            'country_code' => null,
            'method' => 'POST',
            'content_length' => null,
            'status_code' => null,
            'user_agent_id' => null,
            'mime_type_id' => null,
            'url_id' => null,
            'referer_url_id' => null,
            'origin_url_id' => null,
        ]);
    }

    #[Test]
    public function testRelations(): void
    {
        $ipAddress = IpAddress::factory()->create();
        $userAgent = UserAgent::factory()->create();
        $mimeType = MimeType::factory()->create();
        $url = Url::factory()->create();
        $refererUrl = Url::factory()->create();
        $originUrl = Url::factory()->create();

        $loggedRequest = LoggedRequest::factory()->create([
            'ip_address_id' => $ipAddress->id,
            'user_agent_id' => $userAgent->id,
            'mime_type_id' => $mimeType->id,
            'url_id' => $url->id,
            'referer_url_id' => $refererUrl->id,
            'origin_url_id' => $originUrl->id,
        ]);

        $this->assertEquals($ipAddress->id, $loggedRequest->ipAddress->id);
        $this->assertEquals($userAgent->id, $loggedRequest->userAgent->id);
        $this->assertEquals($mimeType->id, $loggedRequest->mimeType->id);
        $this->assertEquals($url->id, $loggedRequest->url->id);
        $this->assertEquals($refererUrl->id, $loggedRequest->refererUrl->id);
        $this->assertEquals($originUrl->id, $loggedRequest->originUrl->id);
    }
}
