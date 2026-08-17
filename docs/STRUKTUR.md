# Struktur Folder

Letak file yang dibuat/diubah untuk fitur baru (monitoring, chart 7 hari, log aktivitas, bagi hasil transparan).

## Backend (app/)

```
app/
├── Http/Controllers/
│   ├── Auth/
│   │   └── AuthController.php            # login/register/logout + log 'register'
│   ├── Admin/
│   │   ├── ActivityLogController.php     # [BARU] index log aktivitas (read-only)
│   │   ├── MonitoringController.php      # [BARU] index + show monitoring gerobak
│   │   ├── ProductController.php         # CRUD produk (observer mencatat log)
│   │   └── UserController.php            # kelola pengguna (observer mencatat log)
│   ├── CartController.php                # [DIUBAH] simpan commission_items + log ambil_gerobak
│   ├── CommissionController.php          # [DIUBAH] load items + persen dari config
│   ├── DashboardController.php           # [DIUBAH] query agregat 7 hari + data chart
│   └── Controller.php
├── Models/
│   ├── ActivityLog.php                   # [BARU] model log (tanpa updated_at)
│   ├── CommissionItem.php                # [BARU] model rincian bagi hasil per produk
│   ├── Commission.php                    # [DIUBAH] relasi hasMany items
│   ├── User.php                          # [DIUBAH] relasi hasMany activityLogs
│   ├── Cart.php / CartItem.php / Product.php
├── Observers/
│   ├── CartObserver.php                  # [BARU] log retur_gerobak
│   ├── CommissionObserver.php            # [BARU] log ambil_komisi
│   ├── ProductObserver.php               # [BARU] log CRUD produk
│   └── UserObserver.php                  # [BARU] log tambah/hapus user
├── Providers/
│   └── AppServiceProvider.php            # [DIUBAH] register observer + event login/logout
└── Support/
    └── ActivityLogger.php                # [BARU] helper pencatatan log
```

## Konfigurasi

```
config/
└── komisi.php                            # [BARU] persentase_bagi_hasil (0.20)
```

## Database

```
database/
├── migrations/
│   ├── 2026_08_17_000001_create_activity_logs_table.php      # [BARU]
│   └── 2026_08_17_000002_create_commission_items_table.php   # [BARU]
└── seeders/                              # akun awal + produk contoh
```

## Frontend (resources/views/)

```
resources/views/
├── layouts/
│   └── app.blade.php                     # [DIUBAH] menu admin: Monitoring Gerobak & Log Aktivitas
├── dashboard/
│   └── index.blade.php                   # [DIUBAH] tabel isi gerobak aktif
├── carts/
│   └── retur.blade.php                   # [DIUBAH] live preview estimasi keuntungan & bagian
├── commissions/
│   └── index.blade.php                   # [DIUBAH] accordion rincian transparan per produk
└── admin/
    ├── dashboard.blade.php               # [DIUBAH] line chart Chart.js 7 hari + dropdown filter
    ├── carts/
    │   ├── index.blade.php               # [BARU] monitoring (tab aktif/dikembalikan + cari)
    │   └── show.blade.php                # [BARU] detail gerobak + hasil bagi
    └── activity/
        └── index.blade.php               # [BARU] log aktivitas read-only + filter
```

## Route & Dokumentasi

```
routes/
└── web.php                               # [DIUBAH] admin/gerobak, admin/gerobak/{gerobak}, admin/log-aktivitas

docs/
├── ERD.md                                # [BARU] skema tabel + relasi PK/FK
├── ALUR.md                               # [BARU] alur navigasi lengkap
└── STRUKTUR.md                           # file ini

README.md                                 # [DIUBAH] dokumentasi proyek
```

## Dependensi Frontend (CDN, tanpa build)

- Bootstrap 5.3.3 + Bootstrap Icons (layouts/app.blade.php)
- Chart.js 4.4.3 (admin/dashboard.blade.php)
