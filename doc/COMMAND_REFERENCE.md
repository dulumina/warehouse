# Command Reference - Warehouse Management System

## Setup Commands

### Run All Migrations

```bash
php artisan migrate
```

Creates all 19 warehouse system tables.

### Seed Roles & Permissions

```bash
php artisan db:seed --class=WarehousePermissionSeeder
```

Creates:

- 4 roles (Super Admin, Warehouse Manager, Warehouse Staff, Viewer)
- 24 permissions
- Role-permission mappings

### Rollback & Reset (Development)

```bash
# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Reset and re-run all migrations
php artisan migrate:refresh

# Refresh and seed
php artisan migrate:refresh --seed
```

## Tinker Commands (for testing)

### Generate Test Data

```bash
php artisan tinker

# Create warehouses
>>> Warehouse::factory(3)->create();

# Create categories
>>> $category = Category::factory()->create();

# Create units
>>> $unit = Unit::factory()->create();

# Create products with relationships
>>> Product::factory(10)->create([
...   'category_id' => $category->id,
...   'unit_id' => $unit->id,
... ]);

# Create suppliers
>>> Supplier::factory(5)->create();

# Create a warehouse with manager
>>> $user = User::find(1);
>>> Warehouse::factory()->create(['manager_id' => $user->id]);

# Exit
>>> exit
```

## API Testing with cURL

### Get Bearer Token

```bash
# Register new user
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123"
  }'

# Or login
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'

# Save the token from response
export TOKEN="your_token_here"
```

### Warehouse Operations

```bash
# List warehouses
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/warehouses

# Create warehouse
curl -X POST http://localhost:8000/api/v1/warehouses \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "WH001",
    "name": "Main Warehouse",
    "address": "123 Storage Lane",
    "city": "Jakarta",
    "province": "DKI Jakarta",
    "postal_code": "12345",
    "phone": "+62212345678",
    "email": "warehouse@example.com"
  }'

# View warehouse
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/warehouses/{warehouse_id}

# Update warehouse
curl -X PUT http://localhost:8000/api/v1/warehouses/{warehouse_id} \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "Updated Name"}'

# Delete warehouse
curl -X DELETE http://localhost:8000/api/v1/warehouses/{warehouse_id} \
  -H "Authorization: Bearer $TOKEN"

# Get warehouse locations
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/warehouses/{warehouse_id}/locations
```

### Product Operations

```bash
# List products
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/products

# Create product
curl -X POST http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "SKU-001",
    "barcode": "1234567890",
    "name": "Widget A",
    "category_id": "{category_uuid}",
    "unit_id": "{unit_uuid}",
    "type": "FINISHED_GOOD",
    "min_stock": 10,
    "max_stock": 100,
    "reorder_point": 20,
    "standard_cost": 50000,
    "selling_price": 75000
  }'

# Get product
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/products/{product_id}

# Get product inventory
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/products/{product_id}/inventory
```

### Inventory Operations

```bash
# Get all inventory
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/inventory

# Get warehouse inventory
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/inventory/warehouse/{warehouse_id}

# Get product inventory across warehouses
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/inventory/product/{product_id}

# Get low stock items
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/inventory/low-stock

# Get expiring items
curl -H "Authorization: Bearer $TOKEN" \
  "http://localhost:8000/api/v1/inventory/expiring?days=30"
```

### Stock In Operations

```bash
# Create stock in
curl -X POST http://localhost:8000/api/v1/stock-ins \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "warehouse_id": "{warehouse_uuid}",
    "supplier_id": "{supplier_uuid}",
    "type": "PURCHASE",
    "transaction_date": "2026-01-26",
    "reference_number": "PO-001",
    "items": [
      {
        "product_id": "{product_uuid}",
        "quantity": 100,
        "unit_cost": 50000
      }
    ]
  }'

# List stock ins
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/stock-ins

# Get stock in detail
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/stock-ins/{stock_in_id}

# Mark as pending
curl -X POST http://localhost:8000/api/v1/stock-ins/{stock_in_id}/pending \
  -H "Authorization: Bearer $TOKEN"

# Approve stock in
curl -X POST http://localhost:8000/api/v1/stock-ins/{stock_in_id}/approve \
  -H "Authorization: Bearer $TOKEN"

# Reject stock in
curl -X POST http://localhost:8000/api/v1/stock-ins/{stock_in_id}/reject \
  -H "Authorization: Bearer $TOKEN"

# Delete stock in (only draft)
curl -X DELETE http://localhost:8000/api/v1/stock-ins/{stock_in_id} \
  -H "Authorization: Bearer $TOKEN"
```

### Stock Out Operations

```bash
# Create stock out
curl -X POST http://localhost:8000/api/v1/stock-outs \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "warehouse_id": "{warehouse_uuid}",
    "type": "SALES",
    "transaction_date": "2026-01-26",
    "reference_number": "SO-001",
    "items": [
      {
        "product_id": "{product_uuid}",
        "quantity": 50,
        "unit_cost": 50000
      }
    ]
  }'

# List stock outs
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/stock-outs

# Get stock out detail
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/stock-outs/{stock_out_id}

# Mark as pending
curl -X POST http://localhost:8000/api/v1/stock-outs/{stock_out_id}/pending \
  -H "Authorization: Bearer $TOKEN"

# Approve stock out
curl -X POST http://localhost:8000/api/v1/stock-outs/{stock_out_id}/approve \
  -H "Authorization: Bearer $TOKEN"

# Reject stock out
curl -X POST http://localhost:8000/api/v1/stock-outs/{stock_out_id}/reject \
  -H "Authorization: Bearer $TOKEN"
```

### Stock Transfer Operations

```bash
# Create transfer
curl -X POST http://localhost:8000/api/v1/stock-transfers \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "from_warehouse_id": "{source_warehouse_uuid}",
    "to_warehouse_id": "{dest_warehouse_uuid}",
    "transaction_date": "2026-01-26",
    "items": [
      {
        "product_id": "{product_uuid}",
        "quantity": 50
      }
    ]
  }'

# List transfers
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/stock-transfers

# Get transfer detail
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/stock-transfers/{transfer_id}

# Send transfer
curl -X POST http://localhost:8000/api/v1/stock-transfers/{transfer_id}/send \
  -H "Authorization: Bearer $TOKEN"

# Receive transfer
curl -X POST http://localhost:8000/api/v1/stock-transfers/{transfer_id}/receive \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "items_received": {
      "{item_id}": 50
    }
  }'
```

### Stock Adjustment Operations

```bash
# Create adjustment
curl -X POST http://localhost:8000/api/v1/stock-adjustments \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "warehouse_id": "{warehouse_uuid}",
    "type": "PHYSICAL_COUNT",
    "adjustment_date": "2026-01-26",
    "items": [
      {
        "product_id": "{product_uuid}",
        "actual_quantity": 95,
        "unit_cost": 50000,
        "reason": "Physical count difference"
      }
    ]
  }'

# List adjustments
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/stock-adjustments

# Get adjustment detail
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8000/api/v1/stock-adjustments/{adjustment_id}

# Approve adjustment
curl -X POST http://localhost:8000/api/v1/stock-adjustments/{adjustment_id}/approve \
  -H "Authorization: Bearer $TOKEN"

# Reject adjustment
curl -X POST http://localhost:8000/api/v1/stock-adjustments/{adjustment_id}/reject \
  -H "Authorization: Bearer $TOKEN"
```

## Testing Commands

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/WarehouseTest.php

# Run specific test method
php artisan test tests/Feature/WarehouseTest.php --filter=test_can_create_warehouse

# Run with coverage
php artisan test --coverage

# Run in parallel
php artisan test --parallel
```

### Create Test Database

```bash
# Use SQLite for tests
# Add to .env.testing
DATABASE_DRIVER=sqlite
DATABASE_DATABASE=:memory:

# Run migrations for tests
php artisan migrate --env=testing
```

## Database Commands

### View Database Tables

```bash
php artisan db:table {table_name}

# Example: view warehouses table
php artisan db:table warehouses
```

### Database Seeding

```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=WarehousePermissionSeeder

# Refresh and seed
php artisan migrate:fresh --seed
```

## Development Commands

### Clear Cache

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Or all at once
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear
```

### Monitor Logs

```bash
# Watch logs in real-time
tail -f storage/logs/laravel.log

# Or use Laravel's log viewer
php artisan log:tail
```

### Generate API Documentation

```bash
# Create OpenAPI/Swagger docs
# (you'll need to install scribe or similar)
php artisan scribe:generate
```

## Optimization Commands

### Optimize Application

```bash
# Optimize autoloader
composer dump-autoload --optimize

# Optimize Laravel
php artisan optimize

# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

## Common Troubleshooting

### Migration Issues

```bash
# If migrations are stuck
php artisan migrate:refresh

# Check migration status
php artisan migrate:status

# Rollback to specific batch
php artisan migrate:rollback --step=1
```

### Permission Issues

```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache

# On Windows, the above may not work. Instead:
icacls "storage" /grant:r "%username%:F" /inheritance:e
icacls "bootstrap\cache" /grant:r "%username%:F" /inheritance:e
```

### Clear Everything (Development Only)

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:forget spatie.permission.cache
composer dump-autoload
```

## All Endpoints Quick Reference

```
WAREHOUSES (6)
GET    /api/v1/warehouses
POST   /api/v1/warehouses
GET    /api/v1/warehouses/{warehouse}
PUT    /api/v1/warehouses/{warehouse}
DELETE /api/v1/warehouses/{warehouse}
GET    /api/v1/warehouses/{warehouse}/locations

PRODUCTS (6)
GET    /api/v1/products
POST   /api/v1/products
GET    /api/v1/products/{product}
PUT    /api/v1/products/{product}
DELETE /api/v1/products/{product}
GET    /api/v1/products/{product}/inventory

INVENTORY (5)
GET    /api/v1/inventory
GET    /api/v1/inventory/warehouse/{warehouse}
GET    /api/v1/inventory/product/{product}
GET    /api/v1/inventory/low-stock
GET    /api/v1/inventory/expiring

STOCK IN (7)
GET    /api/v1/stock-ins
POST   /api/v1/stock-ins
GET    /api/v1/stock-ins/{stockIn}
DELETE /api/v1/stock-ins/{stockIn}
POST   /api/v1/stock-ins/{stockIn}/pending
POST   /api/v1/stock-ins/{stockIn}/approve
POST   /api/v1/stock-ins/{stockIn}/reject

STOCK OUT (7)
GET    /api/v1/stock-outs
POST   /api/v1/stock-outs
GET    /api/v1/stock-outs/{stockOut}
DELETE /api/v1/stock-outs/{stockOut}
POST   /api/v1/stock-outs/{stockOut}/pending
POST   /api/v1/stock-outs/{stockOut}/approve
POST   /api/v1/stock-outs/{stockOut}/reject

STOCK TRANSFER (6)
GET    /api/v1/stock-transfers
POST   /api/v1/stock-transfers
GET    /api/v1/stock-transfers/{transfer}
DELETE /api/v1/stock-transfers/{transfer}
POST   /api/v1/stock-transfers/{transfer}/send
POST   /api/v1/stock-transfers/{transfer}/receive

STOCK ADJUSTMENT (4)
GET    /api/v1/stock-adjustments
POST   /api/v1/stock-adjustments
GET    /api/v1/stock-adjustments/{adjustment}
DELETE /api/v1/stock-adjustments/{adjustment}
POST   /api/v1/stock-adjustments/{adjustment}/approve
POST   /api/v1/stock-adjustments/{adjustment}/reject
```

## Next Steps

1. Run migrations: `php artisan migrate`
2. Seed permissions: `php artisan db:seed --class=WarehousePermissionSeeder`
3. Create test data using Tinker
4. Test API endpoints with cURL
5. Review documentation in WAREHOUSE_SYSTEM.md and SETUP_GUIDE.md
