<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['super_admin', 'ong_representant', 'ong_agent'] as $name) {
            Role::firstOrCreate(['name' => $name]);
        }
    }
}
