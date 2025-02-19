<?php

namespace SlProjects\LaravelRequestLogger\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SlProjects\LaravelRequestLogger\Database\Factories\LoggedRequestFactory;

class LoggedRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address_id',
        'country_code',
        'method',
        'content_length',
        'status_code',
        'user_agent_id',
        'mime_type_id',
        'url_id',
        'referer_url_id',
        'origin_url_id',
    ];

    public function ipAddress(): BelongsTo
    {
        return $this->belongsTo(IpAddress::class);
    }

    public function userAgent(): BelongsTo
    {
        return $this->belongsTo(UserAgent::class);
    }

    public function mimeType(): BelongsTo
    {
        return $this->belongsTo(MimeType::class);
    }

    public function url(): BelongsTo
    {
        return $this->belongsTo(Url::class);
    }

    public function refererUrl(): BelongsTo
    {
        return $this->belongsTo(Url::class, 'referer_url_id');
    }

    public function originUrl(): BelongsTo
    {
        return $this->belongsTo(Url::class, 'origin_url_id');
    }

    protected static function newFactory(): LoggedRequestFactory
    {
        return LoggedRequestFactory::new();
    }
}
