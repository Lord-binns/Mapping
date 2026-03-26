<?php

namespace Database\Factories;

use App\Models\Pin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PinFactory extends Factory
{
    protected $model = Pin::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'latitude' => $this->faker->latitude(14.0, 15.0), // Philippines approx
            'longitude' => $this->faker->longitude(120.5, 121.5),
            'type' => $this->faker->randomElement(['incident', 'dumping', 'flood', 'water', 'hotspot']),
            'status' => $this->faker->randomElement(['pending', 'verified', 'resolved']),
            'image' => $this->faker->imageUrl(400, 300, 'nature'),
            'is_public' => $this->faker->boolean(80),
        ];
    }
}

