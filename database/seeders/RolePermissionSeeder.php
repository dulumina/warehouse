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
            'view dashboard',
            'create post',
            'edit post',
            'delete post',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
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