<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(3),
            'status' => fake()->randomElement(['open', 'in_progress', 'closed']),
            'created_by' => User::where('role', 'user')->inRandomOrder()->first()->id,
            'assigned_to' => fake()->boolean(70) ? User::where('role', 'agent')->inRandomOrder()->first()->id : null,
            'resolved_by' => function (array $attributes) {
                return $attributes['status'] === 'closed'
                    ? User::where('role', 'agent')->inRandomOrder()->first()->id
                    : null;
            },
            'resolved_at' => function (array $attributes) {
                return $attributes['status'] === 'closed' ? fake()->dateTimeBetween('-1 month') : null;
            },
        ];
    }
}
