<?php

namespace Database\Factories;

use App\Enums\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => \App\Models\Event::factory(),
            'type' => $this->faker->randomElement(TicketType::values()),
            'price' => $this->faker->randomNumber(2, true) * 10000,
            'stock' => $this->faker->numberBetween(10, 200),
        ];
    }
}
