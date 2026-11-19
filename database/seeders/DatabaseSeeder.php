<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(HotelCatalogSeeder::class);
        $this->call(HotelContentSeeder::class);
        $this->call(DemoReviewSeeder::class);
    }
}
