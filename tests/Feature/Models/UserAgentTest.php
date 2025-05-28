<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Orchestra\Testbench\Concerns\WithWorkbench;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use SlProjects\LaravelRequestLogger\app\Models\UserAgent;
use Tests\TestCase;

#[CoversClass(UserAgent::class)]
class UserAgentTest extends TestCase
{
    use RefreshDatabase, WithWorkbench;

    #[Test]
    public function testCanCreateInstanceWithValidUserAgent(): void
    {
        $userAgent = UserAgent::factory()->create([
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ]);

        $this->assertDatabaseHas('user_agents', $userAgent->toArray());
    }

    #[Test]
    public function testCanCreateInstanceWithEmptyUserAgent(): void
    {
        $userAgent = UserAgent::factory()->create([
            'user_agent' => '',
        ]);

        $this->assertDatabaseHas('user_agents', $userAgent->toArray());
    }

    #[Test]
    public function testGetIdOrCreateNoData(): void
    {
        $userAgentId = UserAgent::getIdOrCreate('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

        $this->assertIsInt($userAgentId);
        $this->assertDatabaseHas('user_agents', ['id' => $userAgentId, 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36']);
    }

    #[Test]
    public function testGetIdOrCreateExistingUserAgentNotInCache(): void
    {
        $reference = UserAgent::factory()->create([
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ]);

        $userAgentId = UserAgent::getIdOrCreate('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        $this->assertIsInt($userAgentId);
        $this->assertEquals($reference->id, $userAgentId);
    }

    #[Test]
    public function testGetIdOrCreateExistingUserAgentInCache(): void
    {
        $reference = UserAgent::factory()->create([
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ]);

        Cache::expects('has')->andReturn($reference->id);
        Cache::expects('get')->andReturn($reference->id);

        $userAgentId = UserAgent::getIdOrCreate('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        $this->assertIsInt($userAgentId);
        $this->assertEquals($reference->id, $userAgentId);
    }
}