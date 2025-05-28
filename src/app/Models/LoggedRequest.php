<?php

namespace SlProjects\LaravelRequestLogger\app\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SlProjects\LaravelRequestLogger\Database\Factories\LoggedRequestFactory;
use SlProjects\LaravelRequestLogger\Enums\HttpMethod;

/**
 * @property int $id
 * @property int $ip_address_id
 * @property string|null $country_code
 * @property HttpMethod $method
 * @property int|null $content_length
 * @property int|null $status_code
 * @property int|null $user_agent_id
 * @property int|null $mime_type_id
 * @property int|null $url_id
 * @property int|null $referer_url_id
 * @property int|null $origin_url_id
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read IpAddress $ipAddress
 * @property-read UserAgent|null $userAgent
 * @property-read MimeType|null $mimeType
 * @property-read Url|null $url
 * @property-read Url|null $refererUrl
 * @property-read Url|null $originUrl
 */
class LoggedRequest extends Model
{
    /** @use HasFactory<LoggedRequestFactory> */
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

    protected $casts = [
        'ip_address_id' => 'integer',
        'country_code' => 'string',
        'method' => HttpMethod::class,
        'content_length' => 'integer',
        'status_code' => 'integer',
        'user_agent_id' => 'integer',
        'mime_type_id' => 'integer',
        'url_id' => 'integer',
        'referer_url_id' => 'integer',
        'origin_url_id' => 'integer',
    ];

    /** @return BelongsTo<IpAddress, $this> */
    public function ipAddress(): BelongsTo
    {
        return $this->belongsTo(IpAddress::class);
    }

    /** @return BelongsTo<UserAgent, $this> */
    public function userAgent(): BelongsTo
    {
        return $this->belongsTo(UserAgent::class);
    }

    /** @return BelongsTo<MimeType, $this> */
    public function mimeType(): BelongsTo
    {
        return $this->belongsTo(MimeType::class);
    }

    /** @return BelongsTo<Url, $this> */
    public function url(): BelongsTo
    {
        return $this->belongsTo(Url::class);
    }

    /** @return BelongsTo<Url, $this> */
    public function refererUrl(): BelongsTo
    {
        return $this->belongsTo(Url::class, 'referer_url_id');
    }

    /** @return BelongsTo<Url, $this> */
    public function originUrl(): BelongsTo
    {
        return $this->belongsTo(Url::class, 'origin_url_id');
    }

    protected static function newFactory(): LoggedRequestFactory
    {
        return LoggedRequestFactory::new();
    }
}
