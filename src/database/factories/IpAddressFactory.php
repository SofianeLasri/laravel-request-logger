<?php

namespace SlProjects\LaravelRequestLogger\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SlProjects\LaravelRequestLogger\app\Models\IpAddress;

class IpAddressFactory extends Factory
{
    protected $model = IpAddress::class;

    public function definition(): array
    {
        return [
            'ip' => $this->faker->ipv4(),
        ];
    }
}
