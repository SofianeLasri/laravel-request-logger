<?php

namespace SlProjects\LaravelRequestLogger\app\Jobs;

use Carbon\Carbon;
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

    /** @var array{'ip': string, 'country_code': string, 'url': string, 'method': string, 'user_agent': ?string, 'referer': ?string, 'origin': ?string, 'content_type': ?string, 'content_length': ?int, 'status_code': ?int, 'user_id': ?int, 'created_at': Carbon}[] */
    public array $requests;

    /**
     * @param array{'ip': string, 'country_code': string, 'url': string, 'method': string, 'user_agent': ?string, 'referer': ?string, 'origin': ?string, 'content_type': ?string, 'content_length': ?int, 'status_code': ?int, 'user_id': ?int, 'created_at': Carbon}[] $requests
     */
    public function __construct(array $requests)
    {
        $this->requests = $requests;
    }

    public function handle(): void
    {
        $requestsToInsert = [];

        foreach ($this->requests as $request) {
            $requestsToInsert[] = [
                'ip_address_id' => IpAddress::getIdOrCreate($request['ip']),
                'country_code' => $request['country_code'],
                'url_id' => Url::getIdOrCreate($request['url']),
                'method' => $request['method'],
                'user_agent_id' => !empty($request['user_agent']) ? UserAgent::getIdOrCreate($request['user_agent']) : null,
                'referer_url_id' => !empty($request['referer']) ? Url::getIdOrCreate($request['referer']) : null,
                'origin_url_id' => !empty($request['origin']) ? Url::getIdOrCreate($request['origin']) : null,
                'mime_type_id' => !empty($request['content_type']) ? MimeType::getIdOrCreate($request['content_type']) : null,
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
