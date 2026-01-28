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
            // Dashboard
            ['name' => 'view dashboard', 'feature' => 'Dashboard'],

            // Warehouse Management
            ['name' => 'view warehouses', 'feature' => 'Warehouse Management'],
            ['name' => 'create warehouse', 'feature' => 'Warehouse Management'],
            ['name' => 'edit warehouse', 'feature' => 'Warehouse Management'],
            ['name' => 'delete warehouse', 'feature' => 'Warehouse Management'],
            ['name' => 'manage warehouse locations', 'feature' => 'Warehouse Management'],

            // Product Management
            ['name' => 'view products', 'feature' => 'Product Management'],
            ['name' => 'create product', 'feature' => 'Product Management'],
            ['name' => 'edit product', 'feature' => 'Product Management'],
            ['name' => 'delete product', 'feature' => 'Product Management'],
            ['name' => 'manage categories', 'feature' => 'Product Management'],
            ['name' => 'manage suppliers', 'feature' => 'Product Management'],

            // Inventory Management
            ['name' => 'view inventory', 'feature' => 'Inventory Management'],
            ['name' => 'adjust inventory', 'feature' => 'Inventory Management'],
            ['name' => 'view batch info', 'feature' => 'Inventory Management'],
            ['name' => 'manage serial numbers', 'feature' => 'Inventory Management'],

            // Stock Transaction Management
            ['name' => 'create stock in', 'feature' => 'Stock Transactions'],
            ['name' => 'approve stock in', 'feature' => 'Stock Transactions'],
            ['name' => 'create stock out', 'feature' => 'Stock Transactions'],
            ['name' => 'approve stock out', 'feature' => 'Stock Transactions'],
            ['name' => 'create stock transfer', 'feature' => 'Stock Transactions'],
            ['name' => 'approve stock transfer', 'feature' => 'Stock Transactions'],
            ['name' => 'create stock adjustment', 'feature' => 'Stock Transactions'],
            ['name' => 'approve stock adjustment', 'feature' => 'Stock Transactions'],

            // Reporting
            ['name' => 'view stock reports', 'feature' => 'Reporting'],
            ['name' => 'view movement reports', 'feature' => 'Reporting'],
            ['name' => 'view valuation reports', 'feature' => 'Reporting'],
            ['name' => 'export reports', 'feature' => 'Reporting'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['feature' => $permission['feature']]
            );
        }

        // === ROLES ===
        $superAdmin = Role::firstOrCreate(['name' => 'super admin']);
        $manager = Role::firstOrCreate(['name' => 'warehouse manager']);
        $staff = Role::firstOrCreate(['name' => 'warehouse staff']);
        $viewer = Role::firstOrCreate(['name' => 'viewer']);

        // === ASSIGN PERMISSION TO ROLES ===
        // Super Admin - All permissions
        $superAdmin->givePermissionTo(Permission::all());

        // Warehouse Manager - All except delete warehouse/product, not just reporting viewer
        $manager->givePermissionTo([
            'view dashboard',
            'view warehouses',
            'create warehouse',
            'edit warehouse',
            'manage warehouse locations',
            'view products',
            'create product',
            'edit product',
            'manage categories',
            'manage suppliers',
            'view inventory',
            'adjust inventory',
            'view batch info',
            'manage serial numbers',
            'create stock in',
            'approve stock in',
            'create stock out',
            'approve stock out',
            'create stock transfer',
            'approve stock transfer',
            'create stock adjustment',
            'approve stock adjustment',
            'view stock reports',
            'view movement reports',
            'view valuation reports',
            'export reports',
        ]);

        // Warehouse Staff - Create transactions and view data
        $staff->givePermissionTo([
            'view dashboard',
            'view warehouses',
            'view products',
            'view inventory',
            'view batch info',
            'create stock in',
            'create stock out',
            'create stock transfer',
            'create stock adjustment',
            'view stock reports',
        ]);

        // Viewer - Read-only access
        $viewer->givePermissionTo([
            'view dashboard',
            'view warehouses',
            'view products',
            'view inventory',
            'view batch info',
            'view stock reports',
            'view movement reports',
            'view valuation reports',
        ]);

        // === GIVE ROLE TO DEFAULT USERS ===
        $adminUser = \App\Models\User::where('email', 'admin@localhost')->first();
        if ($adminUser) {
            $adminUser->assignRole($superAdmin);
        }

        // Create default test users if they don't exist
        $testUsers = [
            ['email' => 'manager@localhost', 'name' => 'Warehouse Manager', 'role' => $manager],
            ['email' => 'staff@localhost', 'name' => 'Warehouse Staff', 'role' => $staff],
            ['email' => 'viewer@localhost', 'name' => 'Viewer', 'role' => $viewer],
        ];

        foreach ($testUsers as $userData) {
            $user = \App\Models\User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            );

            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }
    }
}