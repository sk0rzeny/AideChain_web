<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(TypeAideSeeder::class);
        $this->call(GeographieSeeder::class);
        $this->call(DemoSeeder::class);
        $this->call(BeneficiaireSeeder::class);
    }
}
