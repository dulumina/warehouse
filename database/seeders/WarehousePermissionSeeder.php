<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class WarehousePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create Warehouse Management Permissions
        Permission::firstOrCreate(['name' => 'view warehouses']);
        Permission::firstOrCreate(['name' => 'create warehouse']);
        Permission::firstOrCreate(['name' => 'edit warehouse']);
        Permission::firstOrCreate(['name' => 'delete warehouse']);
        Permission::firstOrCreate(['name' => 'manage warehouse locations']);

        // Create Product Management Permissions
        Permission::firstOrCreate(['name' => 'view products']);
        Permission::firstOrCreate(['name' => 'create product']);
        Permission::firstOrCreate(['name' => 'edit product']);
        Permission::firstOrCreate(['name' => 'delete product']);
        Permission::firstOrCreate(['name' => 'manage categories']);
        Permission::firstOrCreate(['name' => 'manage suppliers']);

        // Create Inventory Management Permissions
        Permission::firstOrCreate(['name' => 'view inventory']);
        Permission::firstOrCreate(['name' => 'adjust inventory']);
        Permission::firstOrCreate(['name' => 'view batch info']);
        Permission::firstOrCreate(['name' => 'manage serial numbers']);

        // Create Transaction Management Permissions
        Permission::firstOrCreate(['name' => 'create stock in']);
        Permission::firstOrCreate(['name' => 'approve stock in']);
        Permission::firstOrCreate(['name' => 'create stock out']);
        Permission::firstOrCreate(['name' => 'approve stock out']);
        Permission::firstOrCreate(['name' => 'create stock transfer']);
        Permission::firstOrCreate(['name' => 'approve stock transfer']);

        // Create Reporting Permissions
        Permission::firstOrCreate(['name' => 'view stock reports']);
        Permission::firstOrCreate(['name' => 'view movement reports']);
        Permission::firstOrCreate(['name' => 'view valuation reports']);
        Permission::firstOrCreate(['name' => 'export reports']);

        // Create Roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $warehouseManager = Role::firstOrCreate(['name' => 'Warehouse Manager']);
        $warehouseStaff = Role::firstOrCreate(['name' => 'Warehouse Staff']);
        $viewer = Role::firstOrCreate(['name' => 'Viewer']);

        // Assign permissions to Super Admin (all permissions)
        $superAdmin->givePermissionTo(Permission::all());

        // Assign permissions to Warehouse Manager
        $warehouseManager->givePermissionTo([
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
            'view stock reports',
            'view movement reports',
            'view valuation reports',
            'export reports',
        ]);

        // Assign permissions to Warehouse Staff
        $warehouseStaff->givePermissionTo([
            'view products',
            'view inventory',
            'view batch info',
            'create stock in',
            'create stock out',
            'create stock transfer',
            'view stock reports',
        ]);

        // Assign permissions to Viewer (read-only)
        $viewer->givePermissionTo([
            'view warehouses',
            'view products',
            'view inventory',
            'view batch info',
            'view stock reports',
            'view movement reports',
            'view valuation reports',
        ]);
    }
}
