<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ProductSupplier;
use App\Models\Batch;
use App\Models\SerialNumber;
use App\Models\Inventory;
use App\Models\StockIn;
use App\Models\StockInItem;
use App\Models\StockOut;
use App\Models\StockOutItem;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockMovement;

class WarehouseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting Database Seeding...');

        // 1. Permissions & Roles
        $this->seedPermissionsAndRoles();

        // 2. Users
        $users = $this->seedUsers();

        // 3. Warehouses & Locations
        $warehouses = $this->seedWarehouses($users);

        // 4. Categories & Units
        $categories = $this->seedCategories();
        $units = $this->seedUnits();

        // 5. Suppliers
        $suppliers = $this->seedSuppliers();

        // 6. Products
        $products = $this->seedProducts($categories, $units);

        // 7. Product Suppliers
        $this->seedProductSuppliers($products, $suppliers);

        // 8. Batches
        $batches = $this->seedBatches($products, $suppliers);

        // 9. Serial Numbers
        $this->seedSerialNumbers($products, $batches, $warehouses);

        // 10. Stock In Transactions
        $stockIns = $this->seedStockIns($warehouses, $suppliers, $users);
        $this->seedStockInItems($stockIns, $products, $warehouses);

        // 11. Update Inventory from Stock Ins
        $this->updateInventoryFromStockIns();

        // 12. Stock Out Transactions
        $stockOuts = $this->seedStockOuts($warehouses, $users);
        $this->seedStockOutItems($stockOuts, $products, $warehouses);

        // 13. Stock Transfers
        $stockTransfers = $this->seedStockTransfers($warehouses, $users);
        $this->seedStockTransferItems($stockTransfers, $products, $warehouses);

        // 14. Stock Adjustments
        $stockAdjustments = $this->seedStockAdjustments($warehouses, $users);
        $this->seedStockAdjustmentItems($stockAdjustments, $products, $warehouses);

        // 15. Stock Movements
        $this->seedStockMovements($products, $warehouses, $users);

        $this->command->info('✅ Database Seeding Completed!');
    }

    private function seedPermissionsAndRoles(): void
    {
        $this->command->info('📋 Seeding Permissions & Roles...');

        // Define permissions by module
        $permissions = [
            // User Management
            'view users', 'create users', 'edit users', 'delete users',

            // Role & Permission Management
            'view roles', 'create roles', 'edit roles', 'delete roles',
            'view permissions', 'create permissions', 'edit permissions', 'delete permissions',

            // Warehouse Management
            'view warehouses', 'create warehouses', 'edit warehouses', 'delete warehouses',

            // Product Management
            'view products', 'create products', 'edit products', 'delete products',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view suppliers', 'create suppliers', 'edit suppliers', 'delete suppliers',

            // Inventory Management
            'view inventory', 'edit inventory',

            // Stock Transactions
            'view stock in', 'create stock in', 'edit stock in', 'delete stock in', 'approve stock in',
            'view stock out', 'create stock out', 'edit stock out', 'delete stock out', 'approve stock out',
            'view stock transfer', 'create stock transfer', 'edit stock transfer', 'delete stock transfer', 'approve stock transfer',
            'view stock adjustment', 'create stock adjustment', 'edit stock adjustment', 'delete stock adjustment', 'approve stock adjustment',

            // Reports
            'view stock reports', 'view movement reports', 'view valuation reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'warehouse_manager', 'guard_name' => 'web']);
        $staffRole = Role::firstOrCreate(['name' => 'warehouse_staff', 'guard_name' => 'web']);
        $viewerRole = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);

        // Assign permissions to roles
        $adminRole->syncPermissions(Permission::all());

        $managerRole->syncPermissions([
            'view warehouses', 'edit warehouses',
            'view products', 'create products', 'edit products',
            'view categories', 'view suppliers',
            'view inventory', 'edit inventory',
            'view stock in', 'create stock in', 'edit stock in', 'approve stock in',
            'view stock out', 'create stock out', 'edit stock out', 'approve stock out',
            'view stock transfer', 'create stock transfer', 'edit stock transfer', 'approve stock transfer',
            'view stock adjustment', 'create stock adjustment', 'edit stock adjustment', 'approve stock adjustment',
            'view stock reports', 'view movement reports', 'view valuation reports',
        ]);

        $staffRole->syncPermissions([
            'view warehouses',
            'view products', 'view categories', 'view suppliers',
            'view inventory',
            'view stock in', 'create stock in', 'edit stock in',
            'view stock out', 'create stock out', 'edit stock out',
            'view stock transfer', 'create stock transfer', 'edit stock transfer',
            'view stock adjustment', 'create stock adjustment', 'edit stock adjustment',
            'view stock reports', 'view movement reports',
        ]);

        $viewerRole->syncPermissions([
            'view warehouses', 'view products', 'view categories', 'view suppliers',
            'view inventory',
            'view stock in', 'view stock out', 'view stock transfer', 'view stock adjustment',
            'view stock reports', 'view movement reports', 'view valuation reports',
        ]);

        $this->command->info('✓ Permissions & Roles created');
    }

    private function seedUsers(): array
    {
        $this->command->info('👤 Seeding Users...');

        $admin = User::firstOrCreate(
            ['email' => 'admin@warehouse.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        $manager1 = User::firstOrCreate(
            ['email' => 'manager1@warehouse.com'],
            [
                'name' => 'John Manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $manager1->assignRole('warehouse_manager');

        $manager2 = User::firstOrCreate(
            ['email' => 'manager2@warehouse.com'],
            [
                'name' => 'Sarah Manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $manager2->assignRole('warehouse_manager');

        $staff1 = User::firstOrCreate(
            ['email' => 'staff1@warehouse.com'],
            [
                'name' => 'Mike Staff',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $staff1->assignRole('warehouse_staff');

        $staff2 = User::firstOrCreate(
            ['email' => 'staff2@warehouse.com'],
            [
                'name' => 'Lisa Staff',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $staff2->assignRole('warehouse_staff');

        $viewer = User::firstOrCreate(
            ['email' => 'viewer@warehouse.com'],
            [
                'name' => 'Viewer User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $viewer->assignRole('viewer');

        $this->command->info('✓ Users created');

        return compact('admin', 'manager1', 'manager2', 'staff1', 'staff2', 'viewer');
    }

    private function seedWarehouses(array $users): array
    {
        $this->command->info('🏭 Seeding Warehouses & Locations...');

        $warehouseData = [
            [
                'code' => 'WH-JKT-001',
                'name' => 'Jakarta Main Warehouse',
                'address' => 'Jl. Raya Industri No. 123',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'postal_code' => '12345',
                'phone' => '021-12345678',
                'email' => 'jakarta@warehouse.com',
                'manager_id' => $users['manager1']->id,
                'is_active' => true,
            ],
            [
                'code' => 'WH-SBY-001',
                'name' => 'Surabaya Warehouse',
                'address' => 'Jl. Industri Raya No. 456',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60123',
                'phone' => '031-87654321',
                'email' => 'surabaya@warehouse.com',
                'manager_id' => $users['manager2']->id,
                'is_active' => true,
            ],
            [
                'code' => 'WH-BDG-001',
                'name' => 'Bandung Warehouse',
                'address' => 'Jl. Soekarno Hatta No. 789',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40123',
                'phone' => '022-98765432',
                'email' => 'bandung@warehouse.com',
                'manager_id' => $users['manager1']->id,
                'is_active' => true,
            ],
        ];

        $warehouses = [];
        foreach ($warehouseData as $data) {
            $warehouse = Warehouse::firstOrCreate(
                ['code' => $data['code']],
                $data
            );
            $warehouses[] = $warehouse;

            // Create locations for each warehouse
            $this->createWarehouseLocations($warehouse);
        }

        $this->command->info('✓ Warehouses & Locations created');

        return $warehouses;
    }

    private function createWarehouseLocations(Warehouse $warehouse): void
    {
        // Use ENUM values: RECEIVING, STORAGE, PICKING, SHIPPING
        $zones = ['RECEIVING', 'STORAGE', 'PICKING', 'SHIPPING'];
        $aisles = [1, 2];
        $racks = ['A', 'B'];
        $levels = [1, 2, 3];

        foreach ($zones as $zoneIndex => $zone) {
            foreach ($aisles as $aisle) {
                foreach ($racks as $rack) {
                    foreach ($levels as $level) {
                        // Use zone index for code generation (R, S, P, H)
                        $zoneCode = substr($zone, 0, 1); // R, S, P, S (H for SHIPPING)
                        if ($zone === 'SHIPPING') $zoneCode = 'H'; // To avoid duplicate 'S'

                        $code = "{$zoneCode}{$aisle}-{$rack}{$level}";
                        WarehouseLocation::firstOrCreate(
                            [
                                'warehouse_id' => $warehouse->id,
                                'code' => $code,
                            ],
                            [
                                'name' => "Location {$code} ({$zone})",
                                'zone' => $zone, // Use actual ENUM value
                                'aisle' => (string)$aisle,
                                'rack' => $rack,
                                'level' => (string)$level,
                                'bin' => '1',
                                'capacity' => rand(100, 500),
                                'is_active' => true,
                            ]
                        );
                    }
                }
            }
        }
    }

    private function seedCategories(): array
    {
        $this->command->info('📂 Seeding Categories...');

        $categories = [
            ['code' => 'CAT-001', 'name' => 'Electronics', 'description' => 'Electronic products and components', 'is_active' => true],
            ['code' => 'CAT-002', 'name' => 'Raw Materials', 'description' => 'Raw materials for production', 'is_active' => true],
            ['code' => 'CAT-003', 'name' => 'Finished Goods', 'description' => 'Finished products ready for sale', 'is_active' => true],
            ['code' => 'CAT-004', 'name' => 'Packaging', 'description' => 'Packaging materials', 'is_active' => true],
            ['code' => 'CAT-005', 'name' => 'Office Supplies', 'description' => 'Office supplies and stationery', 'is_active' => true],
            ['code' => 'CAT-006', 'name' => 'Safety Equipment', 'description' => 'Safety and PPE equipment', 'is_active' => true],
        ];

        $result = [];
        foreach ($categories as $category) {
            $result[] = Category::firstOrCreate(
                ['code' => $category['code']],
                $category
            );
        }

        // Create subcategories
        $electronics = Category::where('code', 'CAT-001')->first();
        Category::firstOrCreate(
            ['code' => 'CAT-001-01'],
            [
                'parent_id' => $electronics->id,
                'name' => 'Mobile Devices',
                'description' => 'Smartphones and tablets',
                'is_active' => true,
            ]
        );
        Category::firstOrCreate(
            ['code' => 'CAT-001-02'],
            [
                'parent_id' => $electronics->id,
                'name' => 'Computer Components',
                'description' => 'PC parts and accessories',
                'is_active' => true,
            ]
        );

        $this->command->info('✓ Categories created');

        return $result;
    }

    private function seedUnits(): array
    {
        $this->command->info('📏 Seeding Units...');

        $units = [
            ['code' => 'PCS', 'name' => 'Pieces', 'symbol' => 'pcs', 'description' => 'Individual pieces'],
            ['code' => 'BOX', 'name' => 'Box', 'symbol' => 'box', 'description' => 'Boxed items'],
            ['code' => 'KG', 'name' => 'Kilogram', 'symbol' => 'kg', 'description' => 'Weight in kilograms'],
            ['code' => 'M', 'name' => 'Meter', 'symbol' => 'm', 'description' => 'Length in meters'],
            ['code' => 'L', 'name' => 'Liter', 'symbol' => 'L', 'description' => 'Volume in liters'],
            ['code' => 'SET', 'name' => 'Set', 'symbol' => 'set', 'description' => 'Set of items'],
            ['code' => 'PACK', 'name' => 'Pack', 'symbol' => 'pack', 'description' => 'Packed items'],
        ];

        $result = [];
        foreach ($units as $unit) {
            $result[] = Unit::firstOrCreate(
                ['code' => $unit['code']],
                $unit
            );
        }

        $this->command->info('✓ Units created');

        return $result;
    }

    private function seedSuppliers(): array
    {
        $this->command->info('🚚 Seeding Suppliers...');

        $suppliers = [
            [
                'code' => 'SUP-001',
                'name' => 'PT. Electronic Supplier Indonesia',
                'contact_person' => 'Budi Santoso',
                'email' => 'budi@electronic-supplier.com',
                'phone' => '021-11111111',
                'address' => 'Jl. Supplier No. 1',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'tax_number' => '01.234.567.8-901.000',
                'payment_term' => 30,
                'is_active' => true,
            ],
            [
                'code' => 'SUP-002',
                'name' => 'CV. Material Prima',
                'contact_person' => 'Siti Rahayu',
                'email' => 'siti@materialprima.com',
                'phone' => '031-22222222',
                'address' => 'Jl. Material No. 2',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'tax_number' => '02.345.678.9-012.000',
                'payment_term' => 45,
                'is_active' => true,
            ],
            [
                'code' => 'SUP-003',
                'name' => 'PT. Packaging Solution',
                'contact_person' => 'Ahmad Yani',
                'email' => 'ahmad@packagingsolution.com',
                'phone' => '022-33333333',
                'address' => 'Jl. Packaging No. 3',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'tax_number' => '03.456.789.0-123.000',
                'payment_term' => 30,
                'is_active' => true,
            ],
            [
                'code' => 'SUP-004',
                'name' => 'Toko Alat Kantor Sejahtera',
                'contact_person' => 'Dewi Lestari',
                'email' => 'dewi@alatkantor.com',
                'phone' => '021-44444444',
                'address' => 'Jl. Perkantoran No. 4',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'tax_number' => '04.567.890.1-234.000',
                'payment_term' => 14,
                'is_active' => true,
            ],
        ];

        $result = [];
        foreach ($suppliers as $supplier) {
            $result[] = Supplier::firstOrCreate(
                ['code' => $supplier['code']],
                $supplier
            );
        }

        $this->command->info('✓ Suppliers created');

        return $result;
    }

    private function seedProducts(array $categories, array $units): array
    {
        $this->command->info('📦 Seeding Products...');

        $pcsUnit = Unit::where('code', 'PCS')->first();
        $boxUnit = Unit::where('code', 'BOX')->first();
        $kgUnit = Unit::where('code', 'KG')->first();
        $setUnit = Unit::where('code', 'SET')->first();

        $electronics = Category::where('code', 'CAT-001')->first();
        $rawMaterials = Category::where('code', 'CAT-002')->first();
        $finishedGoods = Category::where('code', 'CAT-003')->first();
        $packaging = Category::where('code', 'CAT-004')->first();
        $office = Category::where('code', 'CAT-005')->first();

        $products = [
            // Electronics
            [
                'code' => 'PRD-001',
                'barcode' => '8991234567890',
                'name' => 'Smartphone XYZ Pro',
                'description' => 'High-end smartphone with advanced features',
                'category_id' => $electronics->id,
                'unit_id' => $pcsUnit->id,
                'type' => 'FINISHED_GOOD',
                'min_stock' => 10,
                'max_stock' => 100,
                'reorder_point' => 20,
                'standard_cost' => 5000000,
                'selling_price' => 7500000,
                'weight' => 0.2,
                'dimensions' => json_encode(['length' => 15, 'width' => 7, 'height' => 1]),
                'is_batch_tracked' => true,
                'is_serial_tracked' => true,
                'is_active' => true,
            ],
            [
                'code' => 'PRD-002',
                'barcode' => '8991234567891',
                'name' => 'Laptop ABC Ultra',
                'description' => 'Business laptop with high performance',
                'category_id' => $electronics->id,
                'unit_id' => $pcsUnit->id,
                'type' => 'FINISHED_GOOD',
                'min_stock' => 5,
                'max_stock' => 50,
                'reorder_point' => 10,
                'standard_cost' => 10000000,
                'selling_price' => 15000000,
                'weight' => 1.5,
                'dimensions' => json_encode(['length' => 35, 'width' => 25, 'height' => 2]),
                'is_batch_tracked' => true,
                'is_serial_tracked' => true,
                'is_active' => true,
            ],

            // Raw Materials
            [
                'code' => 'PRD-003',
                'barcode' => '8991234567892',
                'name' => 'Aluminum Sheet A5052',
                'description' => 'Industrial aluminum sheet for manufacturing',
                'category_id' => $rawMaterials->id,
                'unit_id' => $kgUnit->id,
                'type' => 'RAW_MATERIAL',
                'min_stock' => 100,
                'max_stock' => 1000,
                'reorder_point' => 200,
                'standard_cost' => 50000,
                'selling_price' => 75000,
                'weight' => 1,
                'dimensions' => json_encode(['length' => 100, 'width' => 50, 'height' => 0.2]),
                'is_batch_tracked' => true,
                'is_serial_tracked' => false,
                'is_active' => true,
            ],
            [
                'code' => 'PRD-004',
                'barcode' => '8991234567893',
                'name' => 'Plastic Resin PP',
                'description' => 'Polypropylene plastic resin',
                'category_id' => $rawMaterials->id,
                'unit_id' => $kgUnit->id,
                'type' => 'RAW_MATERIAL',
                'min_stock' => 500,
                'max_stock' => 5000,
                'reorder_point' => 1000,
                'standard_cost' => 25000,
                'selling_price' => 35000,
                'weight' => 1,
                'dimensions' => null,
                'is_batch_tracked' => true,
                'is_serial_tracked' => false,
                'is_active' => true,
            ],

            // Finished Goods
            [
                'code' => 'PRD-005',
                'barcode' => '8991234567894',
                'name' => 'Premium Headphone Set',
                'description' => 'Wireless headphone with noise cancelling',
                'category_id' => $finishedGoods->id,
                'unit_id' => $setUnit->id,
                'type' => 'FINISHED_GOOD',
                'min_stock' => 20,
                'max_stock' => 200,
                'reorder_point' => 50,
                'standard_cost' => 500000,
                'selling_price' => 850000,
                'weight' => 0.3,
                'dimensions' => json_encode(['length' => 20, 'width' => 18, 'height' => 8]),
                'is_batch_tracked' => true,
                'is_serial_tracked' => false,
                'is_active' => true,
            ],
            [
                'code' => 'PRD-006',
                'barcode' => '8991234567895',
                'name' => 'Wireless Mouse Pro',
                'description' => 'Ergonomic wireless mouse for productivity',
                'category_id' => $finishedGoods->id,
                'unit_id' => $pcsUnit->id,
                'type' => 'FINISHED_GOOD',
                'min_stock' => 30,
                'max_stock' => 300,
                'reorder_point' => 60,
                'standard_cost' => 150000,
                'selling_price' => 250000,
                'weight' => 0.1,
                'dimensions' => json_encode(['length' => 12, 'width' => 7, 'height' => 4]),
                'is_batch_tracked' => false,
                'is_serial_tracked' => false,
                'is_active' => true,
            ],

            // Packaging
            [
                'code' => 'PRD-007',
                'barcode' => '8991234567896',
                'name' => 'Cardboard Box Medium',
                'description' => 'Standard cardboard box for packaging',
                'category_id' => $packaging->id,
                'unit_id' => $pcsUnit->id,
                'type' => 'CONSUMABLE',
                'min_stock' => 100,
                'max_stock' => 1000,
                'reorder_point' => 200,
                'standard_cost' => 5000,
                'selling_price' => 8000,
                'weight' => 0.5,
                'dimensions' => json_encode(['length' => 40, 'width' => 30, 'height' => 30]),
                'is_batch_tracked' => false,
                'is_serial_tracked' => false,
                'is_active' => true,
            ],
            [
                'code' => 'PRD-008',
                'barcode' => '8991234567897',
                'name' => 'Bubble Wrap Roll',
                'description' => 'Protective bubble wrap 100m roll',
                'category_id' => $packaging->id,
                'unit_id' => $pcsUnit->id,
                'type' => 'CONSUMABLE',
                'min_stock' => 50,
                'max_stock' => 500,
                'reorder_point' => 100,
                'standard_cost' => 75000,
                'selling_price' => 120000,
                'weight' => 2,
                'dimensions' => json_encode(['length' => 100, 'width' => 100, 'height' => 10]),
                'is_batch_tracked' => false,
                'is_serial_tracked' => false,
                'is_active' => true,
            ],

            // Office Supplies
            [
                'code' => 'PRD-009',
                'barcode' => '8991234567898',
                'name' => 'A4 Paper Box',
                'description' => 'A4 copy paper 80gsm - 5 reams per box',
                'category_id' => $office->id,
                'unit_id' => $boxUnit->id,
                'type' => 'CONSUMABLE',
                'min_stock' => 20,
                'max_stock' => 200,
                'reorder_point' => 40,
                'standard_cost' => 180000,
                'selling_price' => 250000,
                'weight' => 12.5,
                'dimensions' => json_encode(['length' => 30, 'width' => 21, 'height' => 25]),
                'is_batch_tracked' => false,
                'is_serial_tracked' => false,
                'is_active' => true,
            ],
            [
                'code' => 'PRD-010',
                'barcode' => '8991234567899',
                'name' => 'Ballpoint Pen Box',
                'description' => 'Blue ballpoint pen - 50 pcs per box',
                'category_id' => $office->id,
                'unit_id' => $boxUnit->id,
                'type' => 'CONSUMABLE',
                'min_stock' => 10,
                'max_stock' => 100,
                'reorder_point' => 20,
                'standard_cost' => 50000,
                'selling_price' => 80000,
                'weight' => 0.6,
                'dimensions' => json_encode(['length' => 20, 'width' => 15, 'height' => 5]),
                'is_batch_tracked' => false,
                'is_serial_tracked' => false,
                'is_active' => true,
            ],
        ];

        $result = [];
        foreach ($products as $product) {
            $result[] = Product::firstOrCreate(
                ['code' => $product['code']],
                $product
            );
        }

        $this->command->info('✓ Products created');

        return $result;
    }

    private function seedProductSuppliers(array $products, array $suppliers): void
    {
        $this->command->info('🔗 Seeding Product Suppliers...');

        $productSupplierData = [
            // Smartphone from Electronic Supplier
            [
                'product_id' => Product::where('code', 'PRD-001')->first()->id,
                'supplier_id' => Supplier::where('code', 'SUP-001')->first()->id,
                'supplier_sku' => 'ESP-PHONE-001',
                'lead_time_days' => 7,
                'min_order_qty' => 10,
                'unit_price' => 4800000,
                'is_preferred' => true,
            ],
            // Laptop from Electronic Supplier
            [
                'product_id' => Product::where('code', 'PRD-002')->first()->id,
                'supplier_id' => Supplier::where('code', 'SUP-001')->first()->id,
                'supplier_sku' => 'ESP-LAPTOP-001',
                'lead_time_days' => 10,
                'min_order_qty' => 5,
                'unit_price' => 9500000,
                'is_preferred' => true,
            ],
            // Aluminum from Material Prima
            [
                'product_id' => Product::where('code', 'PRD-003')->first()->id,
                'supplier_id' => Supplier::where('code', 'SUP-002')->first()->id,
                'supplier_sku' => 'MP-ALU-A5052',
                'lead_time_days' => 14,
                'min_order_qty' => 100,
                'unit_price' => 48000,
                'is_preferred' => true,
            ],
            // Plastic Resin from Material Prima
            [
                'product_id' => Product::where('code', 'PRD-004')->first()->id,
                'supplier_id' => Supplier::where('code', 'SUP-002')->first()->id,
                'supplier_sku' => 'MP-PP-001',
                'lead_time_days' => 21,
                'min_order_qty' => 500,
                'unit_price' => 23000,
                'is_preferred' => true,
            ],
            // Cardboard Box from Packaging Solution
            [
                'product_id' => Product::where('code', 'PRD-007')->first()->id,
                'supplier_id' => Supplier::where('code', 'SUP-003')->first()->id,
                'supplier_sku' => 'PS-BOX-MED',
                'lead_time_days' => 5,
                'min_order_qty' => 100,
                'unit_price' => 4500,
                'is_preferred' => true,
            ],
            // Bubble Wrap from Packaging Solution
            [
                'product_id' => Product::where('code', 'PRD-008')->first()->id,
                'supplier_id' => Supplier::where('code', 'SUP-003')->first()->id,
                'supplier_sku' => 'PS-BW-100M',
                'lead_time_days' => 7,
                'min_order_qty' => 50,
                'unit_price' => 72000,
                'is_preferred' => true,
            ],
            // A4 Paper from Office Supplier
            [
                'product_id' => Product::where('code', 'PRD-009')->first()->id,
                'supplier_id' => Supplier::where('code', 'SUP-004')->first()->id,
                'supplier_sku' => 'AKS-A4-BOX',
                'lead_time_days' => 3,
                'min_order_qty' => 20,
                'unit_price' => 175000,
                'is_preferred' => true,
            ],
            // Ballpoint Pen from Office Supplier
            [
                'product_id' => Product::where('code', 'PRD-010')->first()->id,
                'supplier_id' => Supplier::where('code', 'SUP-004')->first()->id,
                'supplier_sku' => 'AKS-PEN-BLUE',
                'lead_time_days' => 3,
                'min_order_qty' => 10,
                'unit_price' => 48000,
                'is_preferred' => true,
            ],
        ];

        foreach ($productSupplierData as $data) {
            ProductSupplier::firstOrCreate(
                [
                    'product_id' => $data['product_id'],
                    'supplier_id' => $data['supplier_id'],
                ],
                $data
            );
        }

        $this->command->info('✓ Product Suppliers created');
    }

    private function seedBatches(array $products, array $suppliers): array
    {
        $this->command->info('📦 Seeding Batches...');

        $batches = [];

        // Smartphone batches
        $smartphone = Product::where('code', 'PRD-001')->first();
        $supplier1 = Supplier::where('code', 'SUP-001')->first();

        for ($i = 1; $i <= 3; $i++) {
            $batches[] = Batch::firstOrCreate(
                [
                    'product_id' => $smartphone->id,
                    'batch_number' => 'BATCH-PHONE-2025-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                ],
                [
                    'manufacturing_date' => now()->subMonths(6 - $i),
                    'expiry_date' => now()->addYears(2),
                    'supplier_id' => $supplier1->id,
                    'notes' => 'Smartphone batch import from supplier',
                ]
            );
        }

        // Laptop batches
        $laptop = Product::where('code', 'PRD-002')->first();

        for ($i = 1; $i <= 2; $i++) {
            $batches[] = Batch::firstOrCreate(
                [
                    'product_id' => $laptop->id,
                    'batch_number' => 'BATCH-LAPTOP-2025-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                ],
                [
                    'manufacturing_date' => now()->subMonths(4 - $i),
                    'expiry_date' => now()->addYears(3),
                    'supplier_id' => $supplier1->id,
                    'notes' => 'Laptop batch import from supplier',
                ]
            );
        }

        // Raw material batches
        $aluminum = Product::where('code', 'PRD-003')->first();
        $supplier2 = Supplier::where('code', 'SUP-002')->first();

        for ($i = 1; $i <= 4; $i++) {
            $batches[] = Batch::firstOrCreate(
                [
                    'product_id' => $aluminum->id,
                    'batch_number' => 'BATCH-ALU-2025-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                ],
                [
                    'manufacturing_date' => now()->subMonths(8 - $i),
                    'expiry_date' => now()->addYears(5),
                    'supplier_id' => $supplier2->id,
                    'notes' => 'Aluminum sheet batch',
                ]
            );
        }

        $plastic = Product::where('code', 'PRD-004')->first();

        for ($i = 1; $i <= 3; $i++) {
            $batches[] = Batch::firstOrCreate(
                [
                    'product_id' => $plastic->id,
                    'batch_number' => 'BATCH-PP-2025-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                ],
                [
                    'manufacturing_date' => now()->subMonths(10 - $i),
                    'expiry_date' => now()->addYears(3),
                    'supplier_id' => $supplier2->id,
                    'notes' => 'Plastic resin batch',
                ]
            );
        }

        // Headphone batches
        $headphone = Product::where('code', 'PRD-005')->first();

        for ($i = 1; $i <= 2; $i++) {
            $batches[] = Batch::firstOrCreate(
                [
                    'product_id' => $headphone->id,
                    'batch_number' => 'BATCH-HP-2025-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                ],
                [
                    'manufacturing_date' => now()->subMonths(5 - $i),
                    'expiry_date' => now()->addYears(2),
                    'supplier_id' => $supplier1->id,
                    'notes' => 'Headphone set batch',
                ]
            );
        }

        $this->command->info('✓ Batches created');

        return $batches;
    }

    private function seedSerialNumbers(array $products, array $batches, array $warehouses): void
    {
        $this->command->info('🔢 Seeding Serial Numbers...');

        // Only for serial tracked products
        $smartphone = Product::where('code', 'PRD-001')->first();
        $laptop = Product::where('code', 'PRD-002')->first();

        $phoneBatches = Batch::where('product_id', $smartphone->id)->get();
        $laptopBatches = Batch::where('product_id', $laptop->id)->get();

        $warehouse = $warehouses[0];
        $location = WarehouseLocation::where('warehouse_id', $warehouse->id)->first();

        // Smartphone serials
        foreach ($phoneBatches as $batch) {
            for ($i = 1; $i <= 10; $i++) {
                SerialNumber::firstOrCreate(
                    [
                        'product_id' => $smartphone->id,
                        'serial_number' => 'SN-PHONE-' . $batch->batch_number . '-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    ],
                    [
                        'batch_id' => $batch->id,
                        'status' => 'AVAILABLE',
                        'warehouse_id' => $warehouse->id,
                        'location_id' => $location->id,
                    ]
                );
            }
        }

        // Laptop serials
        foreach ($laptopBatches as $batch) {
            for ($i = 1; $i <= 5; $i++) {
                SerialNumber::firstOrCreate(
                    [
                        'product_id' => $laptop->id,
                        'serial_number' => 'SN-LAPTOP-' . $batch->batch_number . '-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    ],
                    [
                        'batch_id' => $batch->id,
                        'status' => 'AVAILABLE',
                        'warehouse_id' => $warehouse->id,
                        'location_id' => $location->id,
                    ]
                );
            }
        }

        $this->command->info('✓ Serial Numbers created');
    }

    private function seedStockIns(array $warehouses, array $suppliers, array $users): array
    {
        $this->command->info('📥 Seeding Stock Ins...');

        $stockIns = [];

        // Stock In from Supplier 1 - Jakarta Warehouse
        $stockIn1 = StockIn::firstOrCreate(
            ['document_number' => 'SI-2025-001'],
            [
                'transaction_date' => now()->subDays(30),
                'warehouse_id' => $warehouses[0]->id,
                'supplier_id' => $suppliers[0]->id,
                'reference_number' => 'PO-2025-001',
                'type' => 'PURCHASE',
                'status' => 'APPROVED',
                'total_items' => 0,
                'total_quantity' => 0,
                'total_value' => 0,
                'notes' => 'Initial stock purchase from Electronic Supplier',
                'received_by' => $users['staff1']->id,
                'approved_by' => $users['manager1']->id,
                'approved_at' => now()->subDays(29),
            ]
        );
        $stockIns[] = $stockIn1;

        // Stock In from Supplier 2 - Jakarta Warehouse
        $stockIn2 = StockIn::firstOrCreate(
            ['document_number' => 'SI-2025-002'],
            [
                'transaction_date' => now()->subDays(25),
                'warehouse_id' => $warehouses[0]->id,
                'supplier_id' => $suppliers[1]->id,
                'reference_number' => 'PO-2025-002',
                'type' => 'PURCHASE',
                'status' => 'APPROVED',
                'total_items' => 0,
                'total_quantity' => 0,
                'total_value' => 0,
                'notes' => 'Raw materials purchase',
                'received_by' => $users['staff1']->id,
                'approved_by' => $users['manager1']->id,
                'approved_at' => now()->subDays(24),
            ]
        );
        $stockIns[] = $stockIn2;

        // Stock In from Supplier 3 - Surabaya Warehouse
        $stockIn3 = StockIn::firstOrCreate(
            ['document_number' => 'SI-2025-003'],
            [
                'transaction_date' => now()->subDays(20),
                'warehouse_id' => $warehouses[1]->id,
                'supplier_id' => $suppliers[2]->id,
                'reference_number' => 'PO-2025-003',
                'type' => 'PURCHASE',
                'status' => 'APPROVED',
                'total_items' => 0,
                'total_quantity' => 0,
                'total_value' => 0,
                'notes' => 'Packaging materials purchase',
                'received_by' => $users['staff2']->id,
                'approved_by' => $users['manager2']->id,
                'approved_at' => now()->subDays(19),
            ]
        );
        $stockIns[] = $stockIn3;

        // Stock In from Supplier 4 - Bandung Warehouse
        $stockIn4 = StockIn::firstOrCreate(
            ['document_number' => 'SI-2025-004'],
            [
                'transaction_date' => now()->subDays(15),
                'warehouse_id' => $warehouses[2]->id,
                'supplier_id' => $suppliers[3]->id,
                'reference_number' => 'PO-2025-004',
                'type' => 'PURCHASE',
                'status' => 'APPROVED',
                'total_items' => 0,
                'total_quantity' => 0,
                'total_value' => 0,
                'notes' => 'Office supplies purchase',
                'received_by' => $users['staff1']->id,
                'approved_by' => $users['manager1']->id,
                'approved_at' => now()->subDays(14),
            ]
        );
        $stockIns[] = $stockIn4;

        // Pending Stock In
        $stockIn5 = StockIn::firstOrCreate(
            ['document_number' => 'SI-2025-005'],
            [
                'transaction_date' => now()->subDays(5),
                'warehouse_id' => $warehouses[0]->id,
                'supplier_id' => $suppliers[0]->id,
                'reference_number' => 'PO-2025-005',
                'type' => 'PURCHASE',
                'status' => 'PENDING',
                'total_items' => 0,
                'total_quantity' => 0,
                'total_value' => 0,
                'notes' => 'Pending stock in waiting for approval',
                'received_by' => $users['staff1']->id,
            ]
        );
        $stockIns[] = $stockIn5;

        $this->command->info('✓ Stock Ins created');

        return $stockIns;
    }

    private function seedStockInItems(array $stockIns, array $products, array $warehouses): void
    {
        $this->command->info('📋 Seeding Stock In Items...');

        // Stock In 1 Items - Electronics
        $location1 = WarehouseLocation::where('warehouse_id', $warehouses[0]->id)->first();

        $item1 = StockInItem::firstOrCreate(
            [
                'stock_in_id' => $stockIns[0]->id,
                'product_id' => Product::where('code', 'PRD-001')->first()->id,
            ],
            [
                'location_id' => $location1->id,
                'batch_number' => 'BATCH-PHONE-2025-001',
                'quantity' => 50,
                'unit_cost' => 4800000,
                'subtotal' => 50 * 4800000,
                'expiry_date' => now()->addYears(2),
                'notes' => 'Smartphone initial stock',
            ]
        );

        $item2 = StockInItem::firstOrCreate(
            [
                'stock_in_id' => $stockIns[0]->id,
                'product_id' => Product::where('code', 'PRD-002')->first()->id,
            ],
            [
                'location_id' => $location1->id,
                'batch_number' => 'BATCH-LAPTOP-2025-001',
                'quantity' => 20,
                'unit_cost' => 9500000,
                'subtotal' => 20 * 9500000,
                'expiry_date' => now()->addYears(3),
                'notes' => 'Laptop initial stock',
            ]
        );

        // Update Stock In 1 totals
        $stockIns[0]->update([
            'total_items' => 2,
            'total_quantity' => 70,
            'total_value' => $item1->subtotal + $item2->subtotal,
        ]);

        // Stock In 2 Items - Raw Materials
        $item3 = StockInItem::firstOrCreate(
            [
                'stock_in_id' => $stockIns[1]->id,
                'product_id' => Product::where('code', 'PRD-003')->first()->id,
            ],
            [
                'location_id' => $location1->id,
                'batch_number' => 'BATCH-ALU-2025-001',
                'quantity' => 500,
                'unit_cost' => 48000,
                'subtotal' => 500 * 48000,
                'expiry_date' => now()->addYears(5),
                'notes' => 'Aluminum sheet stock',
            ]
        );

        $item4 = StockInItem::firstOrCreate(
            [
                'stock_in_id' => $stockIns[1]->id,
                'product_id' => Product::where('code', 'PRD-004')->first()->id,
            ],
            [
                'location_id' => $location1->id,
                'batch_number' => 'BATCH-PP-2025-001',
                'quantity' => 2000,
                'unit_cost' => 23000,
                'subtotal' => 2000 * 23000,
                'expiry_date' => now()->addYears(3),
                'notes' => 'Plastic resin stock',
            ]
        );

        $stockIns[1]->update([
            'total_items' => 2,
            'total_quantity' => 2500,
            'total_value' => $item3->subtotal + $item4->subtotal,
        ]);

        // Stock In 3 Items - Packaging
        $location2 = WarehouseLocation::where('warehouse_id', $warehouses[1]->id)->first();

        $item5 = StockInItem::firstOrCreate(
            [
                'stock_in_id' => $stockIns[2]->id,
                'product_id' => Product::where('code', 'PRD-007')->first()->id,
            ],
            [
                'location_id' => $location2->id,
                'batch_number' => null,
                'quantity' => 500,
                'unit_cost' => 4500,
                'subtotal' => 500 * 4500,
                'expiry_date' => null,
                'notes' => 'Cardboard boxes',
            ]
        );

        $item6 = StockInItem::firstOrCreate(
            [
                'stock_in_id' => $stockIns[2]->id,
                'product_id' => Product::where('code', 'PRD-008')->first()->id,
            ],
            [
                'location_id' => $location2->id,
                'batch_number' => null,
                'quantity' => 200,
                'unit_cost' => 72000,
                'subtotal' => 200 * 72000,
                'expiry_date' => null,
                'notes' => 'Bubble wrap rolls',
            ]
        );

        $stockIns[2]->update([
            'total_items' => 2,
            'total_quantity' => 700,
            'total_value' => $item5->subtotal + $item6->subtotal,
        ]);

        // Stock In 4 Items - Office Supplies
        $location3 = WarehouseLocation::where('warehouse_id', $warehouses[2]->id)->first();

        $item7 = StockInItem::firstOrCreate(
            [
                'stock_in_id' => $stockIns[3]->id,
                'product_id' => Product::where('code', 'PRD-009')->first()->id,
            ],
            [
                'location_id' => $location3->id,
                'batch_number' => null,
                'quantity' => 100,
                'unit_cost' => 175000,
                'subtotal' => 100 * 175000,
                'expiry_date' => null,
                'notes' => 'A4 paper boxes',
            ]
        );

        $item8 = StockInItem::firstOrCreate(
            [
                'stock_in_id' => $stockIns[3]->id,
                'product_id' => Product::where('code', 'PRD-010')->first()->id,
            ],
            [
                'location_id' => $location3->id,
                'batch_number' => null,
                'quantity' => 50,
                'unit_cost' => 48000,
                'subtotal' => 50 * 48000,
                'expiry_date' => null,
                'notes' => 'Ballpoint pen boxes',
            ]
        );

        $stockIns[3]->update([
            'total_items' => 2,
            'total_quantity' => 150,
            'total_value' => $item7->subtotal + $item8->subtotal,
        ]);

        // Stock In 5 Items - Pending
        $item9 = StockInItem::firstOrCreate(
            [
                'stock_in_id' => $stockIns[4]->id,
                'product_id' => Product::where('code', 'PRD-005')->first()->id,
            ],
            [
                'location_id' => $location1->id,
                'batch_number' => 'BATCH-HP-2025-001',
                'quantity' => 30,
                'unit_cost' => 500000,
                'subtotal' => 30 * 500000,
                'expiry_date' => now()->addYears(2),
                'notes' => 'Headphone sets - pending approval',
            ]
        );

        $stockIns[4]->update([
            'total_items' => 1,
            'total_quantity' => 30,
            'total_value' => $item9->subtotal,
        ]);

        $this->command->info('✓ Stock In Items created');
    }

    private function updateInventoryFromStockIns(): void
    {
        $this->command->info('📦 Updating Inventory from Approved Stock Ins...');

        $approvedStockIns = StockIn::where('status', 'APPROVED')->with('items')->get();

        foreach ($approvedStockIns as $stockIn) {
            foreach ($stockIn->items as $item) {
                $inventory = Inventory::firstOrNew([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $stockIn->warehouse_id,
                    'location_id' => $item->location_id,
                    'batch_number' => $item->batch_number,
                ]);

                $inventory->quantity = ($inventory->quantity ?? 0) + $item->quantity;
                $inventory->unit_cost = $item->unit_cost;
                $inventory->last_stock_in = $stockIn->transaction_date;
                $inventory->save();
            }
        }

        $this->command->info('✓ Inventory updated from Stock Ins');
    }

    private function seedStockOuts(array $warehouses, array $users): array
    {
        $this->command->info('📤 Seeding Stock Outs...');

        $stockOuts = [];

        // Stock Out 1 - Sales
        $stockOut1 = StockOut::firstOrCreate(
            ['document_number' => 'SO-2025-001'],
            [
                'transaction_date' => now()->subDays(20),
                'warehouse_id' => $warehouses[0]->id,
                'customer_name' => 'PT. Customer Sejahtera',
                'reference_number' => 'SO-CUST-001',
                'type' => 'SALES',
                'status' => 'APPROVED',
                'total_items' => 0,
                'total_quantity' => 0,
                'total_value' => 0,
                'notes' => 'Sales to customer',
                'issued_by' => $users['staff1']->id,
                'approved_by' => $users['manager1']->id,
                'approved_at' => now()->subDays(20),
            ]
        );
        $stockOuts[] = $stockOut1;

        // Stock Out 2 - Internal Use
        $stockOut2 = StockOut::firstOrCreate(
            ['document_number' => 'SO-2025-002'],
            [
                'transaction_date' => now()->subDays(15),
                'warehouse_id' => $warehouses[0]->id,
                'customer_name' => 'Internal - R&D Department',
                'reference_number' => 'INT-RD-001',
                'type' => 'PRODUCTION',
                'status' => 'APPROVED',
                'total_items' => 0,
                'total_quantity' => 0,
                'total_value' => 0,
                'notes' => 'Internal use for R&D',
                'issued_by' => $users['staff1']->id,
                'approved_by' => $users['manager1']->id,
                'approved_at' => now()->subDays(15),
            ]
        );
        $stockOuts[] = $stockOut2;

        // Stock Out 3 - Pending
        $stockOut3 = StockOut::firstOrCreate(
            ['document_number' => 'SO-2025-003'],
            [
                'transaction_date' => now()->subDays(3),
                'warehouse_id' => $warehouses[1]->id,
                'customer_name' => 'PT. Customer Baru',
                'reference_number' => 'SO-CUST-002',
                'type' => 'SALES',
                'status' => 'PENDING',
                'total_items' => 0,
                'total_quantity' => 0,
                'total_value' => 0,
                'notes' => 'Pending sales order',
                'issued_by' => $users['staff2']->id,
            ]
        );
        $stockOuts[] = $stockOut3;

        $this->command->info('✓ Stock Outs created');

        return $stockOuts;
    }

    private function seedStockOutItems(array $stockOuts, array $products, array $warehouses): void
    {
        $this->command->info('📋 Seeding Stock Out Items...');

        $location1 = WarehouseLocation::where('warehouse_id', $warehouses[0]->id)->first();
        $location2 = WarehouseLocation::where('warehouse_id', $warehouses[1]->id)->first();

        // Stock Out 1 Items
        $item1 = StockOutItem::firstOrCreate(
            [
                'stock_out_id' => $stockOuts[0]->id,
                'product_id' => Product::where('code', 'PRD-001')->first()->id,
            ],
            [
                'location_id' => $location1->id,
                'batch_number' => 'BATCH-PHONE-2025-001',
                'serial_number' => null,
                'quantity' => 10,
                'unit_cost' => 4800000,
                'subtotal' => 10 * 4800000,
                'notes' => 'Sales order item',
            ]
        );

        $item2 = StockOutItem::firstOrCreate(
            [
                'stock_out_id' => $stockOuts[0]->id,
                'product_id' => Product::where('code', 'PRD-002')->first()->id,
            ],
            [
                'location_id' => $location1->id,
                'batch_number' => 'BATCH-LAPTOP-2025-001',
                'serial_number' => null,
                'quantity' => 5,
                'unit_cost' => 9500000,
                'subtotal' => 5 * 9500000,
                'notes' => 'Sales order item',
            ]
        );

        $stockOuts[0]->update([
            'total_items' => 2,
            'total_quantity' => 15,
            'total_value' => $item1->subtotal + $item2->subtotal,
        ]);

        // Stock Out 2 Items
        $item3 = StockOutItem::firstOrCreate(
            [
                'stock_out_id' => $stockOuts[1]->id,
                'product_id' => Product::where('code', 'PRD-003')->first()->id,
            ],
            [
                'location_id' => $location1->id,
                'batch_number' => 'BATCH-ALU-2025-001',
                'serial_number' => null,
                'quantity' => 50,
                'unit_cost' => 48000,
                'subtotal' => 50 * 48000,
                'notes' => 'Internal use - R&D',
            ]
        );

        $stockOuts[1]->update([
            'total_items' => 1,
            'total_quantity' => 50,
            'total_value' => $item3->subtotal,
        ]);

        // Stock Out 3 Items - Pending
        $item4 = StockOutItem::firstOrCreate(
            [
                'stock_out_id' => $stockOuts[2]->id,
                'product_id' => Product::where('code', 'PRD-007')->first()->id,
            ],
            [
                'location_id' => $location2->id,
                'batch_number' => null,
                'serial_number' => null,
                'quantity' => 100,
                'unit_cost' => 4500,
                'subtotal' => 100 * 4500,
                'notes' => 'Pending sales - cardboard boxes',
            ]
        );

        $stockOuts[2]->update([
            'total_items' => 1,
            'total_quantity' => 100,
            'total_value' => $item4->subtotal,
        ]);

        $this->command->info('✓ Stock Out Items created');
    }

    private function seedStockTransfers(array $warehouses, array $users): array
    {
        $this->command->info('🚛 Seeding Stock Transfers...');

        $stockTransfers = [];

        // Transfer 1 - Completed
        $transfer1 = StockTransfer::firstOrCreate(
            ['document_number' => 'ST-2025-001'],
            [
                'transaction_date' => now()->subDays(10),
                'from_warehouse_id' => $warehouses[0]->id,
                'to_warehouse_id' => $warehouses[1]->id,
                'status' => 'RECEIVED',
                'total_items' => 0,
                'total_quantity' => 0,
                'notes' => 'Transfer from Jakarta to Surabaya',
                'sent_by' => $users['staff1']->id,
                'received_by' => $users['staff2']->id,
                'sent_at' => now()->subDays(10),
                'received_at' => now()->subDays(8),
            ]
        );
        $stockTransfers[] = $transfer1;

        // Transfer 2 - In Transit
        $transfer2 = StockTransfer::firstOrCreate(
            ['document_number' => 'ST-2025-002'],
            [
                'transaction_date' => now()->subDays(5),
                'from_warehouse_id' => $warehouses[0]->id,
                'to_warehouse_id' => $warehouses[2]->id,
                'status' => 'IN_TRANSIT',
                'total_items' => 0,
                'total_quantity' => 0,
                'notes' => 'Transfer from Jakarta to Bandung',
                'sent_by' => $users['staff1']->id,
                'sent_at' => now()->subDays(5),
            ]
        );
        $stockTransfers[] = $transfer2;

        // Transfer 3 - Draft
        $transfer3 = StockTransfer::firstOrCreate(
            ['document_number' => 'ST-2025-003'],
            [
                'transaction_date' => now()->subDays(2),
                'from_warehouse_id' => $warehouses[1]->id,
                'to_warehouse_id' => $warehouses[2]->id,
                'status' => 'DRAFT',
                'total_items' => 0,
                'total_quantity' => 0,
                'notes' => 'Planned transfer - not sent yet',
                'sent_by' => $users['staff2']->id, // Draft created by staff
            ]
        );
        $stockTransfers[] = $transfer3;

        $this->command->info('✓ Stock Transfers created');

        return $stockTransfers;
    }

    private function seedStockTransferItems(array $stockTransfers, array $products, array $warehouses): void
    {
        $this->command->info('📋 Seeding Stock Transfer Items...');

        $locationFrom1 = WarehouseLocation::where('warehouse_id', $warehouses[0]->id)->first();
        $locationTo1 = WarehouseLocation::where('warehouse_id', $warehouses[1]->id)->first();
        $locationTo2 = WarehouseLocation::where('warehouse_id', $warehouses[2]->id)->first();

        // Transfer 1 Items - Received
        $item1 = StockTransferItem::firstOrCreate(
            [
                'stock_transfer_id' => $stockTransfers[0]->id,
                'product_id' => Product::where('code', 'PRD-001')->first()->id,
            ],
            [
                'from_location_id' => $locationFrom1->id,
                'to_location_id' => $locationTo1->id,
                'batch_number' => 'BATCH-PHONE-2025-001',
                'quantity' => 20,
                'quantity_received' => 20,
                'notes' => 'Transfer completed',
            ]
        );

        $stockTransfers[0]->update([
            'total_items' => 1,
            'total_quantity' => 20,
        ]);

        // Transfer 2 Items - In Transit
        $item2 = StockTransferItem::firstOrCreate(
            [
                'stock_transfer_id' => $stockTransfers[1]->id,
                'product_id' => Product::where('code', 'PRD-004')->first()->id,
            ],
            [
                'from_location_id' => $locationFrom1->id,
                'to_location_id' => $locationTo2->id,
                'batch_number' => 'BATCH-PP-2025-001',
                'quantity' => 500,
                'quantity_received' => 0,
                'notes' => 'In transit to Bandung',
            ]
        );

        $stockTransfers[1]->update([
            'total_items' => 1,
            'total_quantity' => 500,
        ]);

        // Transfer 3 Items - Draft
        $item3 = StockTransferItem::firstOrCreate(
            [
                'stock_transfer_id' => $stockTransfers[2]->id,
                'product_id' => Product::where('code', 'PRD-007')->first()->id,
            ],
            [
                'from_location_id' => $locationTo1->id,
                'to_location_id' => $locationTo2->id,
                'batch_number' => null,
                'quantity' => 100,
                'quantity_received' => 0,
                'notes' => 'Draft transfer',
            ]
        );

        $stockTransfers[2]->update([
            'total_items' => 1,
            'total_quantity' => 100,
        ]);

        $this->command->info('✓ Stock Transfer Items created');
    }

    private function seedStockAdjustments(array $warehouses, array $users): array
    {
        $this->command->info('🔧 Seeding Stock Adjustments...');

        $stockAdjustments = [];

        // Adjustment 1 - Approved
        $adjustment1 = StockAdjustment::firstOrCreate(
            ['document_number' => 'SA-2025-001'],
            [
                'adjustment_date' => now()->subDays(12),
                'warehouse_id' => $warehouses[0]->id,
                'type' => 'PHYSICAL_COUNT',
                'status' => 'APPROVED',
                'notes' => 'Physical count adjustment',
                'adjusted_by' => $users['staff1']->id,
                'approved_by' => $users['manager1']->id,
                'approved_at' => now()->subDays(12),
            ]
        );
        $stockAdjustments[] = $adjustment1;

        // Adjustment 2 - Draft
        $adjustment2 = StockAdjustment::firstOrCreate(
            ['document_number' => 'SA-2025-002'],
            [
                'adjustment_date' => now()->subDays(3),
                'warehouse_id' => $warehouses[1]->id,
                'type' => 'DAMAGED',
                'status' => 'DRAFT',
                'notes' => 'Damaged goods adjustment',
                'adjusted_by' => $users['staff2']->id,
            ]
        );
        $stockAdjustments[] = $adjustment2;

        $this->command->info('✓ Stock Adjustments created');

        return $stockAdjustments;
    }

    private function seedStockAdjustmentItems(array $stockAdjustments, array $products, array $warehouses): void
    {
        $this->command->info('📋 Seeding Stock Adjustment Items...');

        $location1 = WarehouseLocation::where('warehouse_id', $warehouses[0]->id)->first();
        $location2 = WarehouseLocation::where('warehouse_id', $warehouses[1]->id)->first();

        // Adjustment 1 Items
        $item1 = StockAdjustmentItem::firstOrCreate(
            [
                'stock_adjustment_id' => $stockAdjustments[0]->id,
                'product_id' => Product::where('code', 'PRD-006')->first()->id,
            ],
            [
                'location_id' => $location1->id,
                'batch_number' => null,
                'system_quantity' => 100,
                'actual_quantity' => 98,
                'difference' => -2,
                'unit_cost' => 150000,
                'value_difference' => -2 * 150000,
                'reason' => 'Physical count discrepancy',
            ]
        );

        // Adjustment 2 Items
        $item2 = StockAdjustmentItem::firstOrCreate(
            [
                'stock_adjustment_id' => $stockAdjustments[1]->id,
                'product_id' => Product::where('code', 'PRD-007')->first()->id,
            ],
            [
                'location_id' => $location2->id,
                'batch_number' => null,
                'system_quantity' => 500,
                'actual_quantity' => 485,
                'difference' => -15,
                'unit_cost' => 4500,
                'value_difference' => -15 * 4500,
                'reason' => 'Damaged during handling',
            ]
        );

        $this->command->info('✓ Stock Adjustment Items created');
    }

    private function seedStockMovements(array $products, array $warehouses, array $users): void
    {
        $this->command->info('📊 Seeding Stock Movements...');

        $location1 = WarehouseLocation::where('warehouse_id', $warehouses[0]->id)->first();

        $smartphone = Product::where('code', 'PRD-001')->first();
        $laptop = Product::where('code', 'PRD-002')->first();

        // Movement 1 - Stock In
        StockMovement::firstOrCreate(
            [
                'reference_number' => 'SI-2025-001',
                'product_id' => $smartphone->id,
            ],
            [
                'warehouse_id' => $warehouses[0]->id,
                'location_id' => $location1->id,
                'batch_number' => 'BATCH-PHONE-2025-001',
                'serial_number' => null,
                'transaction_type' => 'STOCK_IN',
                'reference_id' => StockIn::where('document_number', 'SI-2025-001')->first()->id,
                'quantity' => 50,
                'balance_before' => 0,
                'balance_after' => 50,
                'unit_cost' => 4800000,
                'created_by' => $users['staff1']->id,
            ]
        );

        // Movement 2 - Stock Out
        StockMovement::firstOrCreate(
            [
                'reference_number' => 'SO-2025-001',
                'product_id' => $smartphone->id,
            ],
            [
                'warehouse_id' => $warehouses[0]->id,
                'location_id' => $location1->id,
                'batch_number' => 'BATCH-PHONE-2025-001',
                'serial_number' => null,
                'transaction_type' => 'STOCK_OUT',
                'reference_id' => StockOut::where('document_number', 'SO-2025-001')->first()->id,
                'quantity' => -10,
                'balance_before' => 50,
                'balance_after' => 40,
                'unit_cost' => 4800000,
                'created_by' => $users['staff1']->id,
            ]
        );

        // Movement 3 - Transfer Out
        StockMovement::firstOrCreate(
            [
                'reference_number' => 'ST-2025-001-OUT',
                'product_id' => $smartphone->id,
            ],
            [
                'warehouse_id' => $warehouses[0]->id,
                'location_id' => $location1->id,
                'batch_number' => 'BATCH-PHONE-2025-001',
                'serial_number' => null,
                'transaction_type' => 'TRANSFER_OUT',
                'reference_id' => StockTransfer::where('document_number', 'ST-2025-001')->first()->id,
                'quantity' => -20,
                'balance_before' => 40,
                'balance_after' => 20,
                'unit_cost' => 4800000,
                'created_by' => $users['staff1']->id,
            ]
        );

        // Movement 4 - Laptop Stock In
        StockMovement::firstOrCreate(
            [
                'reference_number' => 'SI-2025-001-LAPTOP',
                'product_id' => $laptop->id,
            ],
            [
                'warehouse_id' => $warehouses[0]->id,
                'location_id' => $location1->id,
                'batch_number' => 'BATCH-LAPTOP-2025-001',
                'serial_number' => null,
                'transaction_type' => 'STOCK_IN',
                'reference_id' => StockIn::where('document_number', 'SI-2025-001')->first()->id,
                'quantity' => 20,
                'balance_before' => 0,
                'balance_after' => 20,
                'unit_cost' => 9500000,
                'created_by' => $users['staff1']->id,
            ]
        );

        $this->command->info('✓ Stock Movements created');
    }
}
