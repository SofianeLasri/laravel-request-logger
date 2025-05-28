<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
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

    #[Test, TestDox('Test getIdOrCreate method with new IP address')]
    public function testGetIdOrCreateNoData(): void
    {
        $ipAddressId = IpAddress::getIdOrCreate('127.0.0.1');

        $this->assertIsInt($ipAddressId);
        $this->assertDatabaseHas('ip_addresses', ['id' => $ipAddressId, 'ip' => '127.0.0.1']);
    }

    #[Test, TestDox('Test getIdOrCreate method with existing IP address but not in cache')]
    public function testGetIdOrCreateExistingIpNotInCache(): void
    {
        $reference = IpAddress::factory()->create([
            'ip' => '127.0.0.1',
        ]);

        $ipAddressId = IpAddress::getIdOrCreate('127.0.0.1');
        $this->assertIsInt($ipAddressId);
        $this->assertEquals($reference->id, $ipAddressId);
    }

    #[Test, TestDox('Test getIdOrCreate method with existing IP address in cache')]
    public function testGetIdOrCreateExistingIpInCache(): void
    {
        $reference = IpAddress::factory()->create([
            'id' => 8,
            'ip' => '127.0.0.1',
        ]);

        Cache::expects('has')->andReturn($reference->id);
        Cache::expects('get')->andReturn($reference->id);

        $ipAddressId = IpAddress::getIdOrCreate('127.0.0.1');
        $this->assertIsInt($ipAddressId);
        $this->assertEquals($reference->id, $ipAddressId);
        $this->assertDatabaseHas('ip_addresses', ['id' => $ipAddressId, 'ip' => '127.0.0.1']);
    }
}
