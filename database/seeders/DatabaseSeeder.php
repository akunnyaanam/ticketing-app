<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Event;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'a@x.com',
            'role' => RoleEnum::ADMIN->value,
        ]);

        $this->call([
            CategorySeeder::class,
            EventSeeder::class,
        ]);
    }
}
