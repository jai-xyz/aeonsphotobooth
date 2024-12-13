<?php

namespace Database\Seeders;

use AdminSeeder;
use App\Models\User;
use Database\Seeders\AdminSeeder as SeedersAdminSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SeedersAdminSeeder::class,
            // Add other seeders here
        ]);
    }
}