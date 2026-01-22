<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cache role & permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // === PERMISSIONS ===
        $permissions = [
            ['name' => 'view dashboard', 'feature' => 'Dashboard'],
            ['name' => 'create post', 'feature' => 'Posts'],
            ['name' => 'edit post', 'feature' => 'Posts'],
            ['name' => 'delete post', 'feature' => 'Posts'],
            ['name' => 'manage users', 'feature' => 'Users'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['feature' => $permission['feature']]
            );
        }

        // === ROLES ===
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $user = Role::firstOrCreate(['name' => 'user']);

        // === ASSIGN PERMISSION TO ROLES ===
        $admin->givePermissionTo(Permission::all());

        $editor->givePermissionTo([
            'view dashboard',
            'create post',
            'edit post',
        ]);

        $user->givePermissionTo([
            'view dashboard',
        ]);

        // === GIVE ROLE TO DEFAULT USER ===
        $defaultUser = \App\Models\User::where('email', 'admin@localhost')->first();
        if ($defaultUser) {
            $defaultUser->assignRole($admin);
        }
    }
}