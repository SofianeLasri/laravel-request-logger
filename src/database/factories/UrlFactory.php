<?php

namespace SlProjects\LaravelRequestLogger\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SlProjects\LaravelRequestLogger\app\Models\Url;

class UrlFactory extends Factory
{
    protected $model = Url::class;

    public function definition(): array
    {
        return [
            'url' => $this->faker->url(),
        ];
    }
}
