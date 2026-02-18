<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'event_date' => fake()->dateTimeBetween('now', '+1 year'),
            'created_by' => User::factory(),
            'access_code' => strtoupper(fake()->bothify('????####')),
            'is_private' => true,
        ];
    }
}
