# Warehouse Management System Implementation

This document describes the complete implementation of the Warehouse Management and Stock system for Agusto.

## Overview

A comprehensive Laravel-based system for managing warehouse operations, inventory tracking, and stock transactions with multi-level approval workflows and detailed audit trails.

## Project Structure

### Database Migrations

All migrations have been created in `database/migrations/` with proper foreign key relationships and indexing:

- `2026_01_26_000001_create_warehouses_table.php` - Warehouse master data
- `2026_01_26_000002_create_warehouse_locations_table.php` - Location hierarchies
- `2026_01_26_000003_create_categories_table.php` - Product categories (hierarchical)
- `2026_01_26_000004_create_units_table.php` - Measurement units
- `2026_01_26_000005_create_suppliers_table.php` - Supplier management
- `2026_01_26_000006_create_products_table.php` - Product master data
- `2026_01_26_000007_create_product_suppliers_table.php` - Product-supplier relationships
- `2026_01_26_000008_create_inventory_table.php` - Real-time inventory tracking
- `2026_01_26_000009_create_batches_table.php` - Batch/lot tracking
- `2026_01_26_000010_create_serial_numbers_table.php` - Serial number tracking
- `2026_01_26_000011_create_stock_ins_table.php` - Stock in transactions
- `2026_01_26_000012_create_stock_in_items_table.php` - Stock in details
- `2026_01_26_000013_create_stock_outs_table.php` - Stock out transactions
- `2026_01_26_000014_create_stock_out_items_table.php` - Stock out details
- `2026_01_26_000015_create_stock_transfers_table.php` - Inter-warehouse transfers
- `2026_01_26_000016_create_stock_transfer_items_table.php` - Transfer details
- `2026_01_26_000017_create_stock_adjustments_table.php` - Stock adjustments
- `2026_01_26_000018_create_stock_adjustment_items_table.php` - Adjustment details
- `2026_01_26_000019_create_stock_movements_table.php` - Audit trail for all movements

### Models

All Eloquent models in `app/Models/` with full relationship definitions:

**Master Data Models:**

- `Warehouse.php` - Warehouse entity with manager and relationships
- `WarehouseLocation.php` - Location hierarchies (Zone > Aisle > Rack > Level > Bin)
- `Category.php` - Hierarchical product categories
- `Unit.php` - Measurement units
- `Supplier.php` - Supplier management
- `Product.php` - Product master data with computed attributes
- `ProductSupplier.php` - Product-supplier relationships

**Inventory Models:**

- `Inventory.php` - Real-time stock levels with availability calculation
- `Batch.php` - Batch/lot tracking with expiry tracking
- `SerialNumber.php` - Serial number management

**Transaction Models:**

- `StockIn.php` - Stock in transactions with approval workflow
- `StockInItem.php` - Stock in line items
- `StockOut.php` - Stock out transactions with approval workflow
- `StockOutItem.php` - Stock out line items
- `StockTransfer.php` - Inter-warehouse transfers
- `StockTransferItem.php` - Transfer line items
- `StockAdjustment.php` - Stock adjustments
- `StockAdjustmentItem.php` - Adjustment line items
- `StockMovement.php` - Audit trail for all stock movements

### Services

Business logic layer in `app/Services/`:

- `InventoryService.php` - Core inventory operations:
    - Add/reduce stock
    - Reserve/release stock
    - Low stock alerts
    - Expiring items detection
    - Warehouse inventory summary
    - Stock movement logging

- `StockInService.php` - Stock in management:
    - Create transactions
    - Add/remove/update items
    - Approval workflow
    - Inventory updates

- `StockOutService.php` - Stock out management:
    - Create transactions
    - Add/remove/update items
    - Approval workflow
    - Inventory updates

- `StockTransferService.php` - Inter-warehouse transfers:
    - Create transfers
    - Send (reduce from source)
    - Receive (add to destination)
    - Partial receipt handling

- `StockAdjustmentService.php` - Inventory adjustments:
    - Create adjustments
    - System vs. actual comparison
    - Approval workflow
    - Inventory correction

### Controllers (API)

RESTful API controllers in `app/Http/Controllers/Api/`:

- `WarehouseController.php` - CRUD for warehouses and locations
- `ProductController.php` - CRUD for products with inventory views
- `InventoryController.php` - Inventory queries and reports
- `StockInController.php` - Stock in transaction management
- `StockOutController.php` - Stock out transaction management
- `StockTransferController.php` - Transfer management
- `StockAdjustmentController.php` - Adjustment management

### Form Requests

Input validation in `app/Http/Requests/`:

- `Warehouse/StoreWarehouseRequest.php`
- `Warehouse/UpdateWarehouseRequest.php`
- `Product/StoreProductRequest.php`
- `StockTransaction/StoreStockInRequest.php`
- `StockTransaction/StoreStockOutRequest.php`
- `StockTransaction/StoreStockTransferRequest.php`
- `StockTransaction/StoreStockAdjustmentRequest.php`

### Permissions & Roles

Seeder in `database/seeders/WarehousePermissionSeeder.php` creates:

**Roles:**

- Super Admin - Full access
- Warehouse Manager - Complete warehouse operations
- Warehouse Staff - Basic stock operations
- Viewer - Read-only access

**Permissions:** (24 total)

- Warehouse Management (5)
- Product Management (6)
- Inventory Management (4)
- Transaction Management (6)
- Reporting (4)

## API Endpoints

### Warehouses

```
GET    /api/v1/warehouses
POST   /api/v1/warehouses
GET    /api/v1/warehouses/{warehouse}
PUT    /api/v1/warehouses/{warehouse}
DELETE /api/v1/warehouses/{warehouse}
GET    /api/v1/warehouses/{warehouse}/locations
```

### Products

```
GET    /api/v1/products
POST   /api/v1/products
GET    /api/v1/products/{product}
PUT    /api/v1/products/{product}
DELETE /api/v1/products/{product}
GET    /api/v1/products/{product}/inventory
```

### Inventory

```
GET    /api/v1/inventory
GET    /api/v1/inventory/warehouse/{warehouse}
GET    /api/v1/inventory/product/{product}
GET    /api/v1/inventory/low-stock
GET    /api/v1/inventory/expiring
```

### Stock Transactions

```
# Stock In
GET    /api/v1/stock-ins
POST   /api/v1/stock-ins
GET    /api/v1/stock-ins/{stockIn}
DELETE /api/v1/stock-ins/{stockIn}
POST   /api/v1/stock-ins/{stockIn}/pending
POST   /api/v1/stock-ins/{stockIn}/approve
POST   /api/v1/stock-ins/{stockIn}/reject

# Stock Out
GET    /api/v1/stock-outs
POST   /api/v1/stock-outs
GET    /api/v1/stock-outs/{stockOut}
DELETE /api/v1/stock-outs/{stockOut}
POST   /api/v1/stock-outs/{stockOut}/pending
POST   /api/v1/stock-outs/{stockOut}/approve
POST   /api/v1/stock-outs/{stockOut}/reject

# Stock Transfer
GET    /api/v1/stock-transfers
POST   /api/v1/stock-transfers
GET    /api/v1/stock-transfers/{transfer}
DELETE /api/v1/stock-transfers/{transfer}
POST   /api/v1/stock-transfers/{transfer}/send
POST   /api/v1/stock-transfers/{transfer}/receive

# Stock Adjustment
GET    /api/v1/stock-adjustments
POST   /api/v1/stock-adjustments
GET    /api/v1/stock-adjustments/{adjustment}
DELETE /api/v1/stock-adjustments/{adjustment}
POST   /api/v1/stock-adjustments/{adjustment}/approve
POST   /api/v1/stock-adjustments/{adjustment}/reject
```

## Data Flows

### Stock In Process

1. Create StockIn (DRAFT)
2. Add items with locations, batches, costs
3. Mark as PENDING (when ready for approval)
4. Manager APPROVES → Inventory updated, StockMovement logged
5. Or REJECT → Transaction cancelled

### Stock Out Process

1. Create StockOut (DRAFT)
2. Add items with location/batch selection
3. Mark as PENDING
4. Manager APPROVES → Inventory reduced, StockMovement logged
5. Or REJECT → Transaction cancelled

### Stock Transfer Process

1. Create StockTransfer (DRAFT)
2. Add items to transfer
3. SEND → Reduces from source warehouse
4. Receives at destination
5. RECEIVE → Adds to destination warehouse with received quantities

### Stock Adjustment Process

1. Create StockAdjustment (DRAFT) - from physical count
2. Add items with system qty vs actual qty
3. System calculates differences and values
4. APPROVE → Adjusts inventory based on differences

## Key Features

### Real-time Inventory

- Automatic stock updates on transaction approval
- Available quantity = Total - Reserved
- Multi-location tracking

### Batch & Serial Tracking

- Batch numbers for expiry tracking
- Serial numbers for high-value items
- FIFO/FEFO support for stock picking

### Audit Trail

- Complete movement history in StockMovement
- User tracking for all actions
- Balance before/after tracking
- Reference linking to transactions

### Approval Workflow

- Draft → Pending → Approved/Rejected
- Role-based approval permissions
- Timestamp tracking

### Stock Alerts

- Low stock detection
- Expiring batch identification
- Overstock warnings

## Setup Instructions

### 1. Run Migrations

```bash
php artisan migrate
```

This creates all tables with proper relationships and indexes.

### 2. Seed Roles and Permissions

```bash
php artisan db:seed --class=WarehousePermissionSeeder
```

Creates all warehouse management roles and permissions.

### 3. Create Test Warehouses and Products

You can use the API endpoints to create test data:

```bash
# Create warehouse
curl -X POST http://localhost:8000/api/v1/warehouses \
  -H "Authorization: Bearer YOUR_TOKEN" \
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

## Authorization

All endpoints require:

- `Authorization: Bearer {token}` header with valid Sanctum token
- User must have appropriate permission for the action

Permissions are checked via `authorize()` calls in controllers.

## Error Handling

All API responses follow standard format:

```json
{
  "success": true/false,
  "message": "...",
  "data": {...} or null,
  "status_code": 200
}
```

Validation errors return 422 with detailed messages.

## Future Enhancements

1. **Reporting Module**
    - Stock summary reports
    - Movement analysis
    - Inventory valuation
    - ABC analysis

2. **Notifications**
    - Low stock alerts
    - Expiring batch warnings
    - Transaction approvals

3. **Advanced Features**
    - Stock reservations for orders
    - Automatic reorder point triggers
    - Barcode scanning
    - Mobile app integration

4. **Dashboard Components**
    - Real-time inventory widgets
    - Transaction status tracking
    - User activity logs

## Database Design Notes

- All IDs use UUID for better scalability
- Soft deletes on master data for data retention
- Proper indexing on frequently queried fields
- Foreign key constraints for data integrity
- Cascade deletes for transaction items
- Restrict deletes for master data references
- Computed attributes for availability calculations

## Performance Considerations

- Inventory records indexed by warehouse and product
- Stock movements indexed by date range
- Eager loading in API responses to prevent N+1 queries
- Pagination for large datasets
- Consider caching for read-heavy operations
