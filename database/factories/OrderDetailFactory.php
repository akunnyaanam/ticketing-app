<?php

namespace Database\Factories;

use App\Actions\Order\CalculateOrderDetailAction;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderDetail>
 */
class OrderDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => \App\Models\Order::factory(),
            'ticket_id' => \App\Models\Ticket::factory(),
            'quantity' => $this->faker->numberBetween(1, 5),
            'sub_total' => 0,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (OrderDetail $detail) {
            CalculateOrderDetailAction::make()->handle($detail);
        });
    }
}
