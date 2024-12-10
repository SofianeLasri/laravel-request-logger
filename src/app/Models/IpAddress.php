<?php

namespace SlProjects\LaravelRequestLogger\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class IpAddress extends Model
{
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

    public function setIpAttribute($value)
    {
        if (!self::validateIp($value)) {
            throw new InvalidArgumentException('Invalid IP address');
        }
        $this->attributes['ip'] = $value;
    }


    public static function getIdFromCacheOrCreate(string $ip): int
    {
        $ipHash = md5($ip);
        $cacheKey = "ip_address_id_{$ipHash}";
        $ipAddressId = Cache::has($cacheKey) ? Cache::get($cacheKey) : null;

        if ($ipAddressId === null) {
            $ipAddress = self::firstOrCreate(['ip' => $ip]);
            $ipAddressId = $ipAddress->id;
            Cache::put($cacheKey, $ipAddressId, 60 * 24);
        }

        return $ipAddressId;
    }
}
