# Warehouse Management System - Quick Start Guide

## What Was Implemented

A complete Laravel-based warehouse management system with:

✅ 19 database migrations (all core tables)
✅ 20 Eloquent models with relationships
✅ 5 business logic services
✅ 7 API controllers (fully functional)
✅ 7 form request validators
✅ Role-based access control (4 roles, 24 permissions)
✅ Approval workflow system
✅ Inventory tracking and auditing
✅ Batch and serial number support

## Installation & Setup

### 1. Run Database Migrations

```bash
php artisan migrate
```

This creates all 19 tables with proper relationships and indexes.

### 2. Seed Roles & Permissions

```bash
php artisan db:seed --class=WarehousePermissionSeeder
```

Creates 4 roles with appropriate permissions:

- **Super Admin**: Full access to all features
- **Warehouse Manager**: Manage warehouses, approve transactions
- **Warehouse Staff**: Create transactions, view inventory
- **Viewer**: Read-only access

### 3. Create Test Data (Optional)

Use the CLI to generate test warehouses and products:

```bash
php artisan tinker

// Create a warehouse
>>> Warehouse::factory()->create();

// Create products with categories and units
>>> $category = Category::factory()->create();
>>> $unit = Unit::factory()->create();
>>> Product::factory(5)->create([
...   'category_id' => $category->id,
...   'unit_id' => $unit->id,
... ]);
```

## API Usage Examples

### Authentication

First, get a token:

```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'
```

Response includes `token` - use this in all subsequent requests:

```bash
Authorization: Bearer {token}
```

### Create a Warehouse

```bash
curl -X POST http://localhost:8000/api/v1/warehouses \
  -H "Authorization: Bearer {token}" \
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
```

### Create a Product

```bash
curl -X POST http://localhost:8000/api/v1/products \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "SKU-001",
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
```

### Create a Stock In Transaction

```bash
curl -X POST http://localhost:8000/api/v1/stock-ins \
  -H "Authorization: Bearer {token}" \
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
```

### Approve a Stock In

```bash
curl -X POST http://localhost:8000/api/v1/stock-ins/{stock_in_id}/approve \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json"
```

This will:

1. Update inventory quantities
2. Log stock movement
3. Set status to APPROVED
4. Record approver and timestamp

### Create a Stock Transfer

```bash
curl -X POST http://localhost:8000/api/v1/stock-transfers \
  -H "Authorization: Bearer {token}" \
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
```

### Send & Receive Transfer

```bash
# Send (reduces from source)
curl -X POST http://localhost:8000/api/v1/stock-transfers/{transfer_id}/send \
  -H "Authorization: Bearer {token}"

# Receive (adds to destination)
curl -X POST http://localhost:8000/api/v1/stock-transfers/{transfer_id}/receive \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "items_received": {
      "{item_id}": 50
    }
  }'
```

### Get Inventory

```bash
# Get all inventory
curl http://localhost:8000/api/v1/inventory \
  -H "Authorization: Bearer {token}"

# Get warehouse inventory
curl http://localhost:8000/api/v1/inventory/warehouse/{warehouse_id} \
  -H "Authorization: Bearer {token}"

# Get product inventory across warehouses
curl http://localhost:8000/api/v1/inventory/product/{product_id} \
  -H "Authorization: Bearer {token}"

# Get low stock items
curl http://localhost:8000/api/v1/inventory/low-stock \
  -H "Authorization: Bearer {token}"
```

### Create Stock Adjustment

```bash
curl -X POST http://localhost:8000/api/v1/stock-adjustments \
  -H "Authorization: Bearer {token}" \
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
```

Then approve:

```bash
curl -X POST http://localhost:8000/api/v1/stock-adjustments/{adjustment_id}/approve \
  -H "Authorization: Bearer {token}"
```

## File Structure

```
app/
├── Models/
│   ├── Warehouse.php
│   ├── WarehouseLocation.php
│   ├── Category.php
│   ├── Unit.php
│   ├── Supplier.php
│   ├── Product.php
│   ├── ProductSupplier.php
│   ├── Inventory.php
│   ├── Batch.php
│   ├── SerialNumber.php
│   ├── StockIn.php
│   ├── StockInItem.php
│   ├── StockOut.php
│   ├── StockOutItem.php
│   ├── StockTransfer.php
│   ├── StockTransferItem.php
│   ├── StockAdjustment.php
│   ├── StockAdjustmentItem.php
│   └── StockMovement.php
├── Services/
│   ├── InventoryService.php
│   ├── StockInService.php
│   ├── StockOutService.php
│   ├── StockTransferService.php
│   └── StockAdjustmentService.php
├── Http/
│   ├── Controllers/Api/
│   │   ├── WarehouseController.php
│   │   ├── ProductController.php
│   │   ├── InventoryController.php
│   │   ├── StockInController.php
│   │   ├── StockOutController.php
│   │   ├── StockTransferController.php
│   │   └── StockAdjustmentController.php
│   └── Requests/
│       ├── Warehouse/
│       ├── Product/
│       └── StockTransaction/
database/
├── migrations/
│   └── 2026_01_26_*.php (19 migrations)
├── factories/
│   ├── WarehouseFactory.php
│   ├── ProductFactory.php
│   ├── CategoryFactory.php
│   ├── UnitFactory.php
│   └── StockInFactory.php
└── seeders/
    └── WarehousePermissionSeeder.php
tests/
├── Feature/
│   ├── WarehouseTest.php
│   └── StockInTest.php
    └── ...
```

## Business Logic Summary

### Inventory Service

- `addStock()` - Add stock and log movement
- `reduceStock()` - Remove stock with logging
- `reserveStock()` - Reserve for orders
- `releaseReservedStock()` - Release reservation
- `getLowStockItems()` - Detect low stock
- `getExpiringItems()` - Find expiring batches

### Stock In Service

- `create()` - Create transaction
- `addItem()` - Add line item
- `markAsPending()` - Ready for approval
- `approve()` - Approve and update inventory
- `reject()` - Reject transaction

### Stock Out Service

- Similar workflow to Stock In
- Reduces inventory on approval
- Supports batch/serial tracking

### Stock Transfer Service

- `send()` - Mark as in transit, reduce from source
- `receive()` - Receive at destination, add to stock
- Supports partial receipts

### Stock Adjustment Service

- `approve()` - Apply system qty corrections
- Calculates differences automatically
- Logs adjustments as stock movements

## Key Features

✅ **Real-time Inventory** - Auto-updates on approval
✅ **Multi-location** - Zone > Aisle > Rack > Level > Bin hierarchy
✅ **Batch Tracking** - Expiry dates and FIFO support
✅ **Serial Numbers** - For high-value items
✅ **Approval Workflow** - Draft → Pending → Approved/Rejected
✅ **Audit Trail** - Complete movement history
✅ **Stock Alerts** - Low stock and expiring items
✅ **Role-based Access** - 4 roles, 24 permissions
✅ **API-First Design** - RESTful endpoints
✅ **Validation** - Form request validators
✅ **Error Handling** - Consistent JSON responses

## Testing

Run tests with:

```bash
php artisan test tests/Feature/WarehouseTest.php
php artisan test tests/Feature/StockInTest.php
```

## Next Steps

1. **Add More Endpoints**
    - Category management
    - Supplier management
    - Batch management
    - Serial number tracking

2. **Create Views**
    - Dashboard
    - Transaction forms
    - Inventory grid
    - Reports

3. **Add Reporting**
    - Stock summary
    - Movement analysis
    - ABC analysis
    - Valuation reports

4. **Implement Notifications**
    - Low stock alerts
    - Approval notifications
    - Expiring batch warnings

5. **Mobile Integration**
    - Barcode scanning
    - Mobile API
    - Offline support

## Documentation Files

- `WAREHOUSE_SYSTEM.md` - Complete system documentation
- `SETUP_GUIDE.md` - This file - Quick start guide
- API endpoints fully documented in WAREHOUSE_SYSTEM.md

## Support

For detailed information, see:

- Database schema in WAREHOUSE_SYSTEM.md
- API endpoints in WAREHOUSE_SYSTEM.md
- Data flows in WAREHOUSE_SYSTEM.md

All code is fully commented and follows Laravel conventions.
