<?php

namespace SlProjects\LaravelRequestLogger\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SlProjects\LaravelRequestLogger\app\Models\IpAddress;
use SlProjects\LaravelRequestLogger\app\Models\MimeType;
use SlProjects\LaravelRequestLogger\app\Models\LoggedRequest as RequestModel;
use SlProjects\LaravelRequestLogger\app\Models\Url;
use SlProjects\LaravelRequestLogger\app\Models\UserAgent;

class SaveRequestsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $requests;

    public function __construct(array $requests)
    {
        $this->requests = $requests;
    }

    public function handle(): void
    {
        $requestsToInsert = [];

        foreach ($this->requests as $request) {
            $requestsToInsert[] = [
                'ip_address_id' => IpAddress::getIdFromCacheOrCreate($request['ip']),
                'country_code' => $request['country_code'],
                'url_id' => Url::getIdFromCacheOrCreate($request['url']),
                'method' => $request['method'],
                'user_agent_id' => !empty($request['user_agent']) ? UserAgent::getIdFromCacheOrCreate($request['user_agent']) : null,
                'referer_url_id' => !empty($request['referer']) ? Url::getIdFromCacheOrCreate($request['referer']) : null,
                'origin_url_id' => !empty($request['origin']) ? Url::getIdFromCacheOrCreate($request['origin']) : null,
                'mime_type_id' => !empty($request['content_type']) ? MimeType::getIdFromCacheOrCreate($request['content_type']) : null,
                'content_length' => !empty($request['content_length']) ? $request['content_length'] : null,
                'status_code' => !empty($request['status_code']) ? $request['status_code'] : null,
                'user_id' => !empty($request['user_id']) ? $request['user_id'] : null,
                'created_at' => $request['created_at'],
                'updated_at' => now(),
            ];
        }

        RequestModel::insert($requestsToInsert);
    }
}
