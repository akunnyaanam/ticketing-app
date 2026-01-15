<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Concert',
            ],
            [
                'name' => 'Workshop',
            ],
            [
                'name' => 'Seminar',
            ],
        ];

        Category::insert($categories);
    }
}
