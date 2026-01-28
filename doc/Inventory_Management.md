# Inventory Management – Business Process Flow

## 1. Master Data Management

```mermaid
flowchart TD
    A[Start] --> B[Input / Update Master Data]
    B --> C{Jenis Data?}

    C -->|User| D[Manage Users]
    C -->|Warehouse| E[Manage Warehouses]
    C -->|Location| F[Manage Warehouse Locations]
    C -->|Product| G[Manage Products]
    C -->|Category| H[Manage Categories]
    C -->|Supplier| I[Manage Suppliers]

    D --> J[Save]
    E --> J
    F --> J
    G --> J
    H --> J
    I --> J

    J --> K[End]
```

---

## 2. Stock In (Penerimaan Barang)

```mermaid
flowchart TD
    A[Start] --> B[Create Stock In Document]
    B --> C[Select Supplier & Warehouse]
    C --> D[Input Stock In Items]
    D --> E[Assign Location]
    E --> F[Submit for Approval]
    F --> G{Approved?}
    G -->|No| H[Rejected / Revise]
    G -->|Yes| I[Update Inventory +]
    I --> J[Log Stock Movement]
    J --> K[End]
```

---

## 3. Stock Out (Pengeluaran Barang)

```mermaid
flowchart TD
    A[Start] --> B[Create Stock Out Document]
    B --> C[Select Warehouse]
    C --> D[Input Stock Out Items]
    D --> E[Check Stock Availability]
    E --> F{Stock Sufficient?}
    F -->|No| G[Reject / Adjust Qty]
    F -->|Yes| H[Submit for Approval]
    H --> I{Approved?}
    I -->|No| G
    I -->|Yes| J[Update Inventory -]
    J --> K[Log Stock Movement]
    K --> L[End]
```

---

## 4. Stock Transfer (Mutasi Stok)

```mermaid
flowchart TD
    A[Start] --> B[Create Stock Transfer]
    B --> C[Select From & To Warehouse]
    C --> D[Input Transfer Items]
    D --> E[Check Source Stock]
    E --> F{Stock Available?}
    F -->|No| G[Reject / Revise]
    F -->|Yes| H[Send Stock]
    H --> I[Reduce Source Inventory]
    I --> J[Receive Stock]
    J --> K[Increase Destination Inventory]
    K --> L[Log Stock Movement]
    L --> M[End]
```

---

## 5. Stock Adjustment (Penyesuaian Stok)

```mermaid
flowchart TD
    A[Start] --> B[Stock Opname]
    B --> C[Input Adjustment Items]
    C --> D[Calculate Difference]
    D --> E[Submit for Approval]
    E --> F{Approved?}
    F -->|No| G[Reject / Revise]
    F -->|Yes| H[Update Inventory +/-]
    H --> I[Log Stock Movement]
    I --> J[End]
```

---

## 6. Inventory Tracking & Traceability

```mermaid
flowchart TD
    A[Transaction Occurs] --> B{Transaction Type}
    B -->|Stock In| C[Increase Inventory]
    B -->|Stock Out| D[Decrease Inventory]
    B -->|Transfer| E[Move Inventory]
    B -->|Adjustment| F[Adjust Inventory]

    C --> G[Update Batch / Serial]
    D --> G
    E --> G
    F --> G

    G --> H[Record Stock Movement]
    H --> I[Inventory Report & Audit Trail]
    I --> J[End]
```
