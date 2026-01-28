<?php

namespace Database\Seeders;

use App\Models\User;
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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@localhost',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);

        $this->call(RolePermissionSeeder::class);
        $this->call(WarehouseSeeder::class);
    }
}
