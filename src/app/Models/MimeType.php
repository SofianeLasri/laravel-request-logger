<?php

namespace SlProjects\LaravelRequestLogger\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MimeType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'mime_type',
    ];

    public static function getIdFromCacheOrCreate(string $mimeType): int
    {
        $mimeTypeHash = md5($mimeType);
        $cacheKey = "mime_type_id_{$mimeTypeHash}";
        $mimeTypeId = Cache::has($cacheKey) ? Cache::get($cacheKey) : null;

        if ($mimeTypeId === null) {
            $mimeType = self::firstOrCreate(['mime_type' => $mimeType]);
            $mimeTypeId = $mimeType->id;
            Cache::put($cacheKey, $mimeTypeId, 60 * 24);
        }

        return $mimeTypeId;
    }
}
