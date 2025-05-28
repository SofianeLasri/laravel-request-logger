<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Orchestra\Testbench\Concerns\WithWorkbench;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use SlProjects\LaravelRequestLogger\app\Models\Url;
use Tests\TestCase;

#[CoversClass(Url::class)]
class UrlTest extends TestCase
{
    use RefreshDatabase, WithWorkbench;

    #[Test]
    public function testCanCreateInstanceWithValidUrl(): void
    {
        $url = Url::factory()->create([
            'url' => 'https://example.com/path',
        ]);

        $this->assertDatabaseHas('urls', $url->toArray());
    }

    #[Test]
    public function testCanCreateInstanceWithRelativeUrl(): void
    {
        $url = Url::factory()->create([
            'url' => '/api/users',
        ]);

        $this->assertDatabaseHas('urls', $url->toArray());
    }

    #[Test]
    public function testGetIdOrCreateNoData(): void
    {
        $urlId = Url::getIdOrCreate('https://example.com/path');

        $this->assertIsInt($urlId);
        $this->assertDatabaseHas('urls', ['id' => $urlId, 'url' => 'https://example.com/path']);
    }

    #[Test]
    public function testGetIdOrCreateExistingUrlNotInCache(): void
    {
        $reference = Url::factory()->create([
            'url' => 'https://example.com/path',
        ]);

        $urlId = Url::getIdOrCreate('https://example.com/path');
        $this->assertIsInt($urlId);
        $this->assertEquals($reference->id, $urlId);
    }

    #[Test]
    public function testGetIdOrCreateExistingUrlInCache(): void
    {
        $reference = Url::factory()->create([
            'url' => 'https://example.com/path',
        ]);

        Cache::expects('has')->andReturn($reference->id);
        Cache::expects('get')->andReturn($reference->id);

        $urlId = Url::getIdOrCreate('https://example.com/path');
        $this->assertIsInt($urlId);
        $this->assertEquals($reference->id, $urlId);
    }
}