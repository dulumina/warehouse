# User Flow Diagrams - Agusto WMS

Dokumen ini menjelaskan alur pengguna (user flow) untuk setiap fitur utama dalam Agusto WMS.

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
