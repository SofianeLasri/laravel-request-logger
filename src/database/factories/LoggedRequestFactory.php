<?php

namespace SlProjects\LaravelRequestLogger\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use SlProjects\LaravelRequestLogger\app\Models\IpAddress;
use SlProjects\LaravelRequestLogger\app\Models\MimeType;
use SlProjects\LaravelRequestLogger\app\Models\LoggedRequest;
use SlProjects\LaravelRequestLogger\app\Models\Url;
use SlProjects\LaravelRequestLogger\app\Models\UserAgent;

class LoggedRequestFactory extends Factory
{
    protected $model = LoggedRequest::class;

    public function definition(): array
    {
        return [
            'country_code' => $this->faker->optional()->numberBetween(1, 999),
            'method' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'CONNECT', 'HEAD', 'OPTIONS', 'TRACE']),
            'content_length' => $this->faker->optional()->numberBetween(0, 10000),
            'status_code' => $this->faker->optional()->numberBetween(100, 599),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'ip_address_id' => IpAddress::factory(),
            'user_agent_id' => UserAgent::factory(),
            'mime_type_id' => MimeType::factory(),
            'url_id' => Url::factory(),
            'referer_url_id' => Url::factory(),
            'origin_url_id' => Url::factory(),
        ];
    }
}