<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // database/seeders/DatabaseSeeder.php
    public function run(): void
    {
        $this->call([
            ProductsTableSeeder::class,
            ApplicationsTableSeeder::class,
            CasesTableSeeder::class,
        ]);
    }
}
