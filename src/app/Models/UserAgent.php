<?php

namespace SlProjects\LaravelRequestLogger\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use SlProjects\LaravelRequestLogger\database\factories\UserAgentFactory;

class UserAgent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_agent',
    ];

    public static function getIdFromCacheOrCreate(string $userAgent): int
    {
        $userAgentHash = md5($userAgent);
        $cacheKey = config('request-logger.models_cache_keys.user_agent') . $userAgentHash;
        $userAgentId = Cache::has($cacheKey) ? Cache::get($cacheKey) : null;

        if ($userAgentId === null) {
            $userAgent = self::firstOrCreate(['user_agent' => $userAgent]);
            $userAgentId = $userAgent->id;
            Cache::put($cacheKey, $userAgentId, 60 * 24);
        }

        return $userAgentId;
    }

    protected static function newFactory(): UserAgentFactory
    {
        return UserAgentFactory::new();
    }
}
