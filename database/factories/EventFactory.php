<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'category_id' => \App\Models\Category::inRandomOrder()->first(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(4),
            'datetime' => $this->faker->dateTimeBetween('-1 week', '+1 week'),
            'location' => $this->faker->address(),
        ];
    }
}
