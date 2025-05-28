<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Orchestra\Testbench\Concerns\WithWorkbench;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use SlProjects\LaravelRequestLogger\app\Models\MimeType;
use Tests\TestCase;

#[CoversClass(MimeType::class)]
class MimeTypeTest extends TestCase
{
    use RefreshDatabase, WithWorkbench;

    #[Test]
    public function testCanCreateInstanceWithValidMimeType(): void
    {
        $mimeType = MimeType::factory()->create([
            'mime_type' => 'application/json',
        ]);

        $this->assertDatabaseHas('mime_types', $mimeType->toArray());
    }

    #[Test]
    public function testCanCreateInstanceWithUnknownMimeType(): void
    {
        $mimeType = MimeType::factory()->create([
            'mime_type' => 'unknown/mime-type',
        ]);

        $this->assertDatabaseHas('mime_types', $mimeType->toArray());
    }

    #[Test]
    public function testGetIdOrCreateNoData(): void
    {
        $mimeTypeId = MimeType::getIdOrCreate('application/json');

        $this->assertIsInt($mimeTypeId);
        $this->assertDatabaseHas('mime_types', ['id' => $mimeTypeId, 'mime_type' => 'application/json']);
    }

    #[Test]
    public function testGetIdOrCreateExistingMimeTypeNotInCache(): void
    {
        $reference = MimeType::factory()->create([
            'mime_type' => 'application/json',
        ]);

        $mimeTypeId = MimeType::getIdOrCreate('application/json');
        $this->assertIsInt($mimeTypeId);
        $this->assertEquals($reference->id, $mimeTypeId);
    }

    #[Test]
    public function testGetIdOrCreateExistingMimeTypeInCache(): void
    {
        $reference = MimeType::factory()->create([
            'mime_type' => 'application/json',
        ]);

        Cache::expects('has')->andReturn($reference->id);
        Cache::expects('get')->andReturn($reference->id);

        $mimeTypeId = MimeType::getIdOrCreate('application/json');
        $this->assertIsInt($mimeTypeId);
        $this->assertEquals($reference->id, $mimeTypeId);
    }
}
