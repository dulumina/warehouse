# 📦 ERD – Inventory & Warehouse Management System

Dokumen ini berisi **Entity Relationship Diagram (ERD)** untuk sistem
**Inventory & Warehouse Management** dengan dukungan:

- Multi Warehouse
- Multi Location (Bin/Rack)
- Batch & Serial Tracking
- Stock In / Out / Transfer / Adjustment
- Stock Ledger (Audit Trail)

---

## 🧠 Konsep Umum

- `inventory` → menyimpan **stok terkini**
- `stock_movements` → menyimpan **riwayat pergerakan stok**
- Semua transaksi menggunakan **header–detail pattern**
- Perubahan stok hanya terjadi pada **status APPROVED / RECEIVED**

---

## 📊 ERD Diagram (Mermaid)

```mermaid
erDiagram

    USERS {
        uuid id PK
        string name
        string email
    }

    WAREHOUSES {
        uuid id PK
        string code
        string name
        uuid manager_id FK
        boolean is_active
    }

    WAREHOUSE_LOCATIONS {
        uuid id PK
        uuid warehouse_id FK
        string code
        string zone
        boolean is_active
    }

    CATEGORIES {
        uuid id PK
        uuid parent_id FK
        string code
        string name
    }

    UNITS {
        uuid id PK
        string code
        string name
    }

    SUPPLIERS {
        uuid id PK
        string code
        string name
        boolean is_active
    }

    PRODUCTS {
        uuid id PK
        string code
        string name
        uuid category_id FK
        uuid unit_id FK
        boolean is_active
    }

    PRODUCT_SUPPLIERS {
        uuid id PK
        uuid product_id FK
        uuid supplier_id FK
        boolean is_preferred
    }

    INVENTORY {
        uuid id PK
        uuid product_id FK
        uuid warehouse_id FK
        uuid location_id FK
        decimal quantity
        decimal reserved_quantity
    }

    BATCHES {
        uuid id PK
        uuid product_id FK
        uuid supplier_id FK
        string batch_number
        date expiry_date
    }

    SERIAL_NUMBERS {
        uuid id PK
        uuid product_id FK
        uuid batch_id FK
        string serial_number
        string status
    }

    STOCK_INS {
        uuid id PK
        string document_number
        uuid warehouse_id FK
        uuid supplier_id FK
        uuid received_by FK
        uuid approved_by FK
        string status
    }

    STOCK_IN_ITEMS {
        uuid id PK
        uuid stock_in_id FK
        uuid product_id FK
        uuid location_id FK
        decimal quantity
    }

    STOCK_OUTS {
        uuid id PK
        string document_number
        uuid warehouse_id FK
        uuid issued_by FK
        uuid approved_by FK
        string status
    }

    STOCK_OUT_ITEMS {
        uuid id PK
        uuid stock_out_id FK
        uuid product_id FK
        uuid location_id FK
        decimal quantity
    }

    STOCK_TRANSFERS {
        uuid id PK
        string document_number
        uuid from_warehouse_id FK
        uuid to_warehouse_id FK
        uuid sent_by FK
        uuid received_by FK
        string status
    }

    STOCK_TRANSFER_ITEMS {
        uuid id PK
        uuid stock_transfer_id FK
        uuid product_id FK
        uuid from_location_id FK
        uuid to_location_id FK
        decimal quantity
    }

    STOCK_ADJUSTMENTS {
        uuid id PK
        string document_number
        uuid warehouse_id FK
        uuid adjusted_by FK
        uuid approved_by FK
        string status
    }

    STOCK_ADJUSTMENT_ITEMS {
        uuid id PK
        uuid stock_adjustment_id FK
        uuid product_id FK
        uuid location_id FK
        decimal difference
    }

    STOCK_MOVEMENTS {
        uuid id PK
        uuid product_id FK
        uuid warehouse_id FK
        uuid location_id FK
        string transaction_type
        uuid reference_id
        decimal quantity
    }

    %% RELATIONSHIPS

    USERS ||--o{ WAREHOUSES : manages
    WAREHOUSES ||--o{ WAREHOUSE_LOCATIONS : has

    CATEGORIES ||--o{ CATEGORIES : parent
    CATEGORIES ||--o{ PRODUCTS : categorizes

    UNITS ||--o{ PRODUCTS : measures

    PRODUCTS ||--o{ PRODUCT_SUPPLIERS : supplied_by
    SUPPLIERS ||--o{ PRODUCT_SUPPLIERS : supplies

    PRODUCTS ||--o{ INVENTORY : stocked
    WAREHOUSES ||--o{ INVENTORY : holds
    WAREHOUSE_LOCATIONS ||--o{ INVENTORY : located_at

    PRODUCTS ||--o{ BATCHES : has
    SUPPLIERS ||--o{ BATCHES : produces
    BATCHES ||--o{ SERIAL_NUMBERS : contains

    WAREHOUSES ||--o{ STOCK_INS : receives
    STOCK_INS ||--o{ STOCK_IN_ITEMS : has
    PRODUCTS ||--o{ STOCK_IN_ITEMS : received

    WAREHOUSES ||--o{ STOCK_OUTS : issues
    STOCK_OUTS ||--o{ STOCK_OUT_ITEMS : has
    PRODUCTS ||--o{ STOCK_OUT_ITEMS : issued

    STOCK_TRANSFERS ||--o{ STOCK_TRANSFER_ITEMS : has
    PRODUCTS ||--o{ STOCK_TRANSFER_ITEMS : moved

    STOCK_ADJUSTMENTS ||--o{ STOCK_ADJUSTMENT_ITEMS : has
    PRODUCTS ||--o{ STOCK_ADJUSTMENT_ITEMS : adjusted

    PRODUCTS ||--o{ STOCK_MOVEMENTS : logged
    WAREHOUSES ||--o{ STOCK_MOVEMENTS : records
    USERS ||--o{ STOCK_MOVEMENTS : created_by
```
