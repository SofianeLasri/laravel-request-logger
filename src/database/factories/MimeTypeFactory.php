<?php

namespace SlProjects\LaravelRequestLogger\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SlProjects\LaravelRequestLogger\app\Models\MimeType;

class MimeTypeFactory extends Factory
{
    protected $model = MimeType::class;

    private $commonMimeTypes = [
        'application/json',
        'application/xml',
        'text/html',
        'text/plain',
        'image/jpeg',
        'image/png',
        'image/avif',
        'image/gif',
        'image/svg+xml',
    ];


    public function definition(): array
    {
        return [
            'mime_type' => $this->faker->randomElement($this->commonMimeTypes),
        ];
    }
}
