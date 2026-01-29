# Agusto - Integrated Warehouse Management System

Agusto adalah sistem manajemen gudang (Warehouse Management System) tingkat lanjut yang dibangun dengan Laravel. Aplikasi ini dirancang untuk memberikan kontrol penuh atas inventaris, pelacakan stok secara real-time, manajemen lokasi multi-gudang, dan alur kerja persetujuan yang terstruktur.

## ✨ Fitur Utama

- **Real-time Inventory Tracking**: Pembaruan stok otomatis saat transaksi disetujui, dengan perhitungan stok tersedia yang akurat (Total - Cadangan).
- **Multi-Warehouse Support**: Kelola banyak gudang dengan struktur lokasi hirarki (Zone > Aisle > Rack > Level > Bin).
- **Advanced Batch & Serial Tracking**: Dukungan untuk nomor batch dengan tanggal kedaluwarsa dan pelacakan nomor seri untuk barang bernilai tinggi.
- **Transaction Management**:
    - **Stock In**: Penerimaan barang dari supplier atau sumber lain.
    - **Stock Out**: Pengeluaran barang untuk pengiriman atau penggunaan.
    - **Stock Transfer**: Perpindahan barang antar gudang dengan sistem kirim-terima.
    - **Stock Adjustment**: Penyesuaian stok berdasarkan hasil stock opname fisik.
- **Approval Workflow**: Alur kerja transaksi yang aman (Draft > Pending > Approved/Rejected) untuk menjaga integritas data.
- **Audit Trail & Stock Movement**: Riwayat pergerakan stok yang lengkap mencatat siapa, kapan, dan mengapa stok berubah, lengkap dengan saldo sebelum dan sesudah.
- **Stock Alerts**: Deteksi otomatis untuk stok rendah (low stock), barang hampir kedaluwarsa (expiring), dan peringatan overstock.
- **Role-Based Access Control (RBAC)**: Manajemen akses pengguna yang granular dengan peran seperti Super Admin, Warehouse Manager, Staff, dan Viewer.

## 🛠️ Teknologi yang Digunakan

- **Framework**: [Laravel 12](https://laravel.com)
- **Database**: MySQL/PostgreSQL dengan UUID sebagai Primary Key untuk skalabilitas.
- **Backend Logic**: Service Layer Pattern untuk memisahkan logika bisnis dari Controller.
- **UI/UX**:
    - **Blade Components**: Untuk modularitas tampilan.
    - **Modernize Layout**: Antarmuka admin yang bersih dan responsif.
    - **Tailwind CSS**: Untuk styling yang fleksibel dan modern.
    - **Chart.js**: Untuk visualisasi data pada dashboard.
- **DataTable**: Integrasi server-side processing untuk manajemen data besar.
- **Authentication**: Laravel Breeze / Laravel Fortify (berdasarkan konfigurasi).
- **Icons**: Tabler Icons.

## 🚀 Persiapan Cepat

1.  **Clone repository** dan masuk ke direktori proyek.
2.  Install dependensi: `composer install` dan `npm install`.
3.  Salin `.env.example` ke `.env` dan konfigurasikan database Anda.
4.  Generate app key: `php artisan key:generate`.
5.  Jalankan migrasi: `php artisan migrate`.
6.  Seed data awal (Permissions & Roles): `php artisan db:seed --class=WarehousePermissionSeeder`.
7.  Jalankan server: `php artisan serve` dan `npm run dev`.

## 📊 ERD Diagram

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

## 1. Alur Master Data (Warehouses, Products, Suppliers, etc.)

Alur standar untuk pengelolaan data master.

```mermaid
graph TD
    A[Sidebar Menu] --> B{Pilih Master Data}
    B -->|Warehouses| C[List Warehouses]
    B -->|Products| D[List Products]
    B -->|Suppliers| E[List Suppliers]
    B -->|Units/Categories| F[List Master]

    C & D & E & F --> G[Tambah Data]
    G --> H[Form Input]
    H --> I[Simpan Data]
    I --> J[Success Flash Message]

    C & D & E & F --> K[Edit Data]
    K --> L[Update Form]
    L --> M[Simpan Perubahan]
    M --> J
```

---

## 2. Alur Transaksi Stock In

Proses penerimaan barang masuk ke gudang.

```mermaid
graph TD
    A[Menu Stock In] --> B[Create Stock In]
    B --> C[Pilih Supplier & Warehouse]
    C --> D[Tambah Items/Produk]
    D --> E[Submit as DRAFT]
    E --> F[Status: DRAFT]
    F --> G[Submit for Approval]
    G --> H[Status: PENDING]

    H --> I{Halaman Approvals}
    I -->|Reject| J[Status: REJECTED]
    I -->|Approve| K[Status: COMPLETED]
    K --> L[Inventory Update]
    K --> M[Movement Log Generated]
```

---

## 3. Alur Transaksi Stock Out

Proses pengeluaran barang dari gudang.

```mermaid
graph TD
    A[Menu Stock Out] --> B[Create Stock Out]
    B --> C[Pilih Warehouse & Type]
    C --> D[Tambah Items & Qty]
    D --> E[Sistem Cek Stok]
    E --> F[Submit for Approval]
    F --> G[Status: PENDING]

    G --> H{Halaman Approvals}
    H -->|Reject| I[Status: REJECTED]
    H -->|Approve| J[Status: COMPLETED]
    J --> K[Inventory Deducted]
    J --> L[Movement Log Generated]
```

---

## 4. Alur Stock Transfer

Proses pemindahan stok antar gudang.

```mermaid
graph TD
    A[Menu Stock Transfer] --> B[Create Transfer]
    B --> C[Pilih Gudang Asal & Tujuan]
    C --> D[Tambah Items]
    D --> E[Submit Transfer]
    E --> F[Status: PENDING]
    F --> G[Action: SEND]
    G --> H[Status: IN_TRANSIT]
    H --> I[Inventory Origin: Decreased]

    I --> J[Action: RECEIVE]
    J --> K[Status: COMPLETED]
    K --> L[Inventory Destination: Increased]
    K --> M[Movement Log Generated]
```

---

## 5. Alur Stock Adjustment

Proses penyesuaian stok (opname/koreksi).

```mermaid
graph TD
    A[Menu Stock Adjustment] --> B[Create Adjustment]
    B --> C[Pilih Warehouse & Type]
    C --> D[Input Items & Diff Qty]
    D --> E[Submit for Approval]
    E --> F[Status: PENDING]

    F --> G{Halaman Approvals}
    G -->|Reject| H[Status: REJECTED]
    G -->|Approve| I[Status: COMPLETED]
    I --> J[Inventory Adjusted]
    I --> K[Movement Log Generated]
```

---

## 6. Alur Laporan (Reports)

Akses data analitik dan riwayat.

```mermaid
graph TD
    A[Sidebar: Reports] --> B{Reports Center}
    B -->|Stock Report| C[Visualisasi Stok Per Gudang]
    B -->|Movements| D[Timeline Transaksi]
    B -->|Valuation| E[Analisis Nilai Aset]

    C & D & E --> F[Filter DataTable]
    F --> G[Pencarian Real-time]
    G --> H[Export/Print Data]
```

---

## 7. Alur Manajemen Akses (Admin)

Pengelolaan pengguna dan izin.

```mermaid
graph TD
    A[Settings Sidebar] --> B{Pilih Menu}
    B -->|Users| C[Manajemen Akun]
    B -->|Roles| D[Definisi Group & Izin]
    B -->|Permissions| E[Daftar Akses Sistem]

    D --> F[Assign Permissions to Role]
    C --> G[Assign Role to User]
    G --> H[User Login with Specific Access]
```

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>
