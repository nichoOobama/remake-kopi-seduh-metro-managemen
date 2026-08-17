# CoffeePaste — Employee Advocacy Commerce

Aplikasi penjualan keliling (gerobak) dengan sistem **bagi hasil transparan** untuk karyawan
dan **monitoring + log aktivitas** untuk admin.

- **Backend**: Laravel 13 (PHP 8.3)
- **Frontend**: Bootstrap 5 + Bootstrap Icons (CDN) + Chart.js (CDN)
- **Database**: MySQL

## Fitur

### Karyawan (Employee)
- Ambil gerobak: pilih produk & jumlah (1 gerobak aktif maksimal, stok gudang berkurang otomatis)
- Kembalikan gerobak: input sisa retur, **live preview estimasi keuntungan & bagian 20%**
- **Bagi hasil transparan**: riwayat komisi lengkap dengan rincian per produk (diambil / sisa / terjual,
  harga jual, harga modal, keuntungan, bagian) + rumus perhitungan
- Ambil penghasilan: komisi pending → saldo (balance)

### Admin
- Dashboard: kartu statistik + **line chart penjualan per karyawan 7 hari terakhir**
  (multi-line, filter per karyawan via dropdown)
- **Monitoring gerobak**: siapa membawa apa & berapa jumlahnya (status aktif),
  siapa mengembalikan apa & berapa sisa retur (status dikembalikan), lengkap dengan detail & hasil bagi
- CRUD produk & pengguna
- **Log aktivitas (read-only)**: seluruh aktivitas user (login, logout, ambil/retur gerobak,
  ambil komisi, CRUD produk/user). Admin **tidak dapat** membuat/mengubah/menghapus log.

## Akun Awal (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@kopiseduh.test` | `password123` |
| Karyawan | `karyawan@kopiseduh.test` | `password123` |

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
# atur koneksi MySQL di .env, lalu:
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

## Persentase Bagi Hasil

Nilai persentase ada di `config/komisi.php` → `persentase_bagi_hasil` (default `0.20` = 20%).
Rincian komisi lama tidak terpengaruh karena nilai disimpan sebagai snapshot di `commission_items`.

## Dokumentasi

- [ERD & Skema Basis Data](docs/ERD.md)
- [Alur Halaman & Navigasi](docs/ALUR.md)
- [Struktur Folder](docs/STRUKTUR.md)

## Struktur Folder Singkat

```
app/
├── Http/Controllers/
│   ├── Auth/AuthController.php        # login, register, logout
│   ├── Admin/                         # area admin
│   │   ├── ActivityLogController.php  # log aktivitas (read-only)
│   │   ├── MonitoringController.php   # monitoring gerobak
│   │   ├── ProductController.php      # CRUD produk
│   │   └── UserController.php         # kelola pengguna
│   ├── CartController.php             # ambil / kembalikan gerobak + bagi hasil
│   ├── CommissionController.php       # riwayat komisi & ambil penghasilan
│   └── DashboardController.php        # dashboard per role + data chart 7 hari
├── Models/                            # User, Product, Cart, CartItem, Commission, CommissionItem, ActivityLog
├── Observers/                         # pencatat log otomatis (Product, User, Cart, Commission)
├── Support/ActivityLogger.php         # helper pencatatan log
└── Providers/AppServiceProvider.php   # register observer + event login/logout

config/komisi.php                      # persentase bagi hasil

database/migrations/                   # skema tabel (lihat docs/ERD.md)
database/seeders/                      # akun awal + produk contoh

resources/views/
├── auth/                              # login, register
├── layouts/app.blade.php              # navbar per role
├── dashboard/index.blade.php          # dashboard karyawan + isi gerobak
├── carts/ambil.blade.php              # form ambil gerobak
├── carts/retur.blade.php              # form retur + live preview
├── commissions/index.blade.php        # komisi transparan (accordion rincian)
└── admin/
    ├── dashboard.blade.php            # statistik + line chart 7 hari
    ├── carts/index.blade.php          # monitoring gerobak (tab aktif/dikembalikan)
    ├── carts/show.blade.php           # detail gerobak
    ├── activity/index.blade.php       # log aktivitas (read-only)
    ├── products/                      # CRUD produk
    └── users/                         # kelola pengguna

docs/                                  # ERD.md, ALUR.md, STRUKTUR.md
routes/web.php                         # seluruh route aplikasi
```
