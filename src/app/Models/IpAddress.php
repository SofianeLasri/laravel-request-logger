<?php

namespace SlProjects\LaravelRequestLogger\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use SlProjects\LaravelRequestLogger\Database\Factories\IpAddressFactory;

/**
 * @property int $id
 * @property string $ip
 */
class IpAddress extends Model
{
    /** @use HasFactory<IpAddressFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'ip',
    ];

    protected $casts = [
        'ip' => 'string',
    ];

    protected static function validateIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    public function setIpAttribute(string $value): void
    {
        if (!self::validateIp($value)) {
            throw new InvalidArgumentException('Invalid IP address');
        }
        $this->attributes['ip'] = $value;
    }

    /**
     * Retrieves the IP address ID or creates a new record if it doesn't exist.
     *
     * @param string $ip The IP address to retrieve or create.
     * @return int The ID of the IP address.
     */
    public static function getIdOrCreate(string $ip): int
    {
        $ipHash = md5($ip);
        $cacheKey = config('request-logger.models_cache_keys.ip_address') . $ipHash;
        $ipAddressId = Cache::has($cacheKey) ? Cache::get($cacheKey) : null;

        if ($ipAddressId === null) {
            $ipAddress = self::firstOrCreate(['ip' => $ip]);
            $ipAddressId = $ipAddress->id;
            Cache::put($cacheKey, $ipAddressId, 60 * 24);
        }

        return $ipAddressId;
    }

    protected static function newFactory(): IpAddressFactory
    {
        return IpAddressFactory::new();
    }
}
