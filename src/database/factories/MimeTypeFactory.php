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
            'mime_type' => $this->generateUniqueMimeType(),
        ];
    }

    private function generateUniqueMimeType(): string
    {
        $availableMimeTypes = array_diff($this->commonMimeTypes, MimeType::pluck('mime_type')->toArray());

        if (!empty($availableMimeTypes)) {
            return $this->faker->randomElement($availableMimeTypes);
        }

        $types = ['application', 'text', 'image', 'video', 'audio'];
        $subtypes = ['json', 'xml', 'html', 'plain', 'jpeg', 'png', 'mp4', 'mpeg', 'custom'];

        do {
            $mimeType = $this->faker->randomElement($types) . '/' . $this->faker->randomElement($subtypes) . '-' . $this->faker->unique()->word();
        } while (MimeType::where('mime_type', $mimeType)->exists());

        return $mimeType;
    }
}
