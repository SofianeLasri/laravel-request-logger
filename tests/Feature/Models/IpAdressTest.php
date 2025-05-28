<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Orchestra\Testbench\Concerns\WithWorkbench;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use SlProjects\LaravelRequestLogger\app\Models\IpAddress;
use Tests\TestCase;

#[CoversClass(IpAddress::class)]
class IpAdressTest extends TestCase
{
    use RefreshDatabase, WithWorkbench;

    #[Test]
    public function testCanCreateInstanceWithValidIp(): void
    {
        $ipAddress = IpAddress::factory()->create([
            'ip' => '127.0.0.1',
        ]);

        $this->assertDatabaseHas('ip_addresses', $ipAddress->toArray());
    }

    #[Test, TestDox('Test that the model check if the IP address is valid')]
    public function testInvalidIpAddress(): void
    {
        try {
            IpAddress::factory()->create([
                'ip' => 'invalid-ip',
            ]);
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('Invalid IP address', $e->getMessage());
        }
    }
}
