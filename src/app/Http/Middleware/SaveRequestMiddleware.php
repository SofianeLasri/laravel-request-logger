<?php

namespace SlProjects\LaravelRequestLogger\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SaveRequestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $cacheKey = config('request-logger.cache_key');
        $ttl = config('request-logger.cache_ttl');

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

        Cache::lock('request_cache_lock', 3)->get(function () use ($cacheKey, $serializedRequest, $ttl) {
            $requests = Cache::get($cacheKey, []);
            $requests[] = $serializedRequest;
            Cache::put($cacheKey, $requests, $ttl);
        });
    }
}
