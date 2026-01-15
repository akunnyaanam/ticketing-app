<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::inRandomOrder()->take(5)->get();
        $users = User::inRandomOrder()->take(5)->get();

        foreach ($events as $event) {
            Order::factory()
                ->for($event)
                ->for($users->random())
                ->hasDetails(rand(1, 3))
                ->create();
        }
    }
}
