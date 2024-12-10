<?php

namespace SlProjects\LaravelRequestLogger\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class UserAgent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_agent',
    ];

    public static function getIdFromCacheOrCreate(string $userAgent): int
    {
        $userAgent = Str::before($userAgent, ';');
        $userAgentHash = md5($userAgent);
        $cacheKey = "user_agent_id_{$userAgentHash}";
        $userAgentId = Cache::has($cacheKey) ? Cache::get($cacheKey) : null;

        if ($userAgentId === null) {
            $userAgent = self::firstOrCreate(['user_agent' => $userAgent]);
            $userAgentId = $userAgent->id;
            Cache::put($cacheKey, $userAgentId, 60 * 24);
        }

        return $userAgentId;
    }
}