<?php

namespace SlProjects\LaravelRequestLogger\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SlProjects\LaravelRequestLogger\app\Models\UserAgent;

class UserAgentFactory extends Factory
{
    protected $model = UserAgent::class;

    public function definition(): array
    {
        return [
            'user_agent' => $this->faker->userAgent(),
        ];
    }
}
