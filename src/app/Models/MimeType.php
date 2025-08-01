<?php

namespace SlProjects\LaravelRequestLogger\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use SlProjects\LaravelRequestLogger\Database\Factories\MimeTypeFactory;

/**
 * @property int $id
 * @property string $mime_type
 */
class MimeType extends Model
{
    /** @use HasFactory<MimeTypeFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'mime_type',
    ];

    public static function getIdOrCreate(string $mimeType): int
    {
        $mimeTypeHash = md5($mimeType);
        $cacheKey = config('request-logger.models_cache_keys.mime_type') . $mimeTypeHash;
        $mimeTypeId = Cache::has($cacheKey) ? Cache::get($cacheKey) : null;

        if ($mimeTypeId === null) {
            $mimeType = self::firstOrCreate(['mime_type' => $mimeType]);
            $mimeTypeId = $mimeType->id;
            Cache::put($cacheKey, $mimeTypeId, 60 * 24);
        }

        return $mimeTypeId;
    }

    protected static function newFactory(): MimeTypeFactory
    {
        return MimeTypeFactory::new();
    }
}
