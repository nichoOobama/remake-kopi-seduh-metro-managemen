# ERD & Skema Basis Data

Skema berikut menggambarkan seluruh tabel beserta relasi Primary Key (PK) dan Foreign Key (FK).

```mermaid
erDiagram
    users ||--o{ carts : "memiliki (1:N)"
    users ||--o{ commissions : "menerima (1:N)"
    users ||--o{ activity_logs : "mencatat (1:N)"

    products ||--o{ cart_items : "diambil (1:N)"
    products |o--o{ commission_items : "snapshot (1:N, nullable)"

    carts ||--o{ cart_items : "berisi (1:N)"
    carts ||--o| commissions : "menghasilkan (1:1)"

    commissions ||--o{ commission_items : "rincian (1:N)"

    users {
        bigint id PK
        string name
        string email "unique"
        string role "admin | employee"
        decimal balance "saldo penghasilan"
    }

    products {
        bigint id PK
        string name
        text description "nullable"
        decimal harga_modal "harga beli (cost)"
        decimal harga_jual
        int stok "unsigned"
        string foto "nullable"
    }

    carts {
        bigint id PK
        bigint user_id FK "users.id"
        enum status "active | returned"
        timestamp taken_at
        timestamp returned_at "nullable"
    }

    cart_items {
        bigint id PK
        bigint cart_id FK "carts.id"
        bigint product_id FK "products.id"
        int qty_ambil
        int qty_sisa
        int qty_terjual "qty_ambil - qty_sisa"
        decimal harga_modal "snapshot"
        decimal harga_jual "snapshot"
    }

    commissions {
        bigint id PK
        bigint cart_id FK "carts.id (unique)"
        bigint user_id FK "users.id"
        decimal total_penjualan
        decimal total_modal
        decimal total_untung
        decimal upah_20persen "bagi hasil karyawan"
        enum status "pending | paid"
        timestamp paid_at "nullable"
    }

    commission_items {
        bigint id PK
        bigint commission_id FK "commissions.id (cascade)"
        bigint product_id FK "products.id (nullOnDelete)"
        string product_name "snapshot nama produk"
        int qty_ambil
        int qty_sisa
        int qty_terjual
        decimal harga_modal "snapshot"
        decimal harga_jual "snapshot"
        decimal subtotal_penjualan "terjual x harga jual"
        decimal subtotal_modal "terjual x harga modal"
        decimal keuntungan "penjualan - modal"
        decimal upah_item "bagi hasil item"
    }

    activity_logs {
        bigint id PK
        bigint user_id FK "users.id (cascade)"
        string action "login, logout, ambil_gerobak, ..."
        text description "nullable"
        string ip_address "nullable"
        string user_agent "nullable"
        timestamp created_at "read-only, tanpa CRUD"
    }
```

## Catatan Desain

1. **Snapshot harga** — `cart_items.harga_modal` / `harga_jual` disimpan saat gerobak diambil agar
   perhitungan bagi hasil tidak berubah walau harga produk diedit admin.
2. **Snapshot rincian komisi** — `commission_items` menyimpan nama produk + perhitungan per item.
   `product_id` memakai `nullOnDelete` sehingga riwayat tetap utuh walau produk dihapus.
   (Perbaikan dari `cart_items` yang masih `cascadeOnDelete`.)
3. **Log aktivitas** — `activity_logs` tidak memiliki route CRUD sama sekali; hanya ditulis otomatis
   oleh observer/event dan dibaca admin.
4. **Satu komisi per gerobak** — `commissions.cart_id` ber-`unique` (1:1).
5. **Persentase bagi hasil** — dari `config/komisi.php`, nilai di-snapshot ke `upah_20persen`
   dan `commission_items.upah_item` saat gerobak dikembalikan.

## Index yang Dipakai

- `activity_logs`: `user_id`, `created_at` (untuk filter & sorting log)
- `commission_items`: `commission_id` (untuk rincian per komisi)
- `commissions`: `cart_id` unique, `user_id` (untuk riwayat karyawan & chart)
