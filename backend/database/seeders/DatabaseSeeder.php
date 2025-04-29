<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategoriesSeeder::class,
            UsersSeeder::class,
            FreelancersSeeder::class,
            ClientsSeeder::class,
            AdminSeeder::class,
            ProjectsSeeder::class,
            BiddingsSeeder::class,
        ]);
    }
}
