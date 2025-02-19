<?php

namespace SlProjects\LaravelRequestLogger\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use SlProjects\LaravelRequestLogger\database\factories\UrlFactory;

class Url extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'url',
    ];

    public static function getIdFromCacheOrCreate(string $url): int
    {
        $urlHash = md5($url);
        $cacheKey = config('request-logger.models_cache_keys.url') . $urlHash;
        $urlId = Cache::has($cacheKey) ? Cache::get($cacheKey) : null;

        if ($urlId === null) {
            $url = self::firstOrCreate(['url' => $url]);
            $urlId = $url->id;
            Cache::put($cacheKey, $urlId, 60 * 24);
        }

        return $urlId;
    }

    protected static function newFactory(): UrlFactory
    {
        return UrlFactory::new();
    }
}
