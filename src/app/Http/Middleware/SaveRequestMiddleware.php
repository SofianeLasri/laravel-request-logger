<?php

namespace SlProjects\LaravelRequestLogger\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SaveRequestMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function terminate(Request $request, $response): void
    {
        $cacheKey = "requests";

        $serializedRequest = [
            'ip' => $request->ip(),
            'country_code' => $request->getPreferredLanguage(),
            'url' => Str::endsWith($request->fullUrl(), '/') ? substr($request->fullUrl(), 0, -1) : $request->fullUrl(),
            'method' => $request->method(),
            'user_agent' => $request->header('User-Agent'),
            'referer' => $request->header('referer'),
            'origin' => $request->header('origin'),
            'content_type' => $request->header('content-type'),
            'content_length' => $request->header('content-length'),
            'status_code' => $response->getStatusCode(),
            'user_id' => $request->user()?->id,
            'created_at' => now(),
        ];

        $ttl = 3600;

        Cache::lock('request_cache_lock', 10)->block(5, function () use ($cacheKey, $serializedRequest, $ttl) {
            $requests = Cache::get($cacheKey, []);
            $requests[] = $serializedRequest;
            Cache::put($cacheKey, $requests, $ttl);
        });
    }
}
