# Alur Halaman & Navigasi

```mermaid
flowchart TD
    A[Landing /] --> B{Sudah login?}
    B -- Tidak --> C[Login]
    B -- Ya, Admin --> D[Dashboard Admin]
    B -- Ya, Karyawan --> E[Dashboard Karyawan]

    C --> F{Login berhasil?}
    F -- Gagal --> C
    F -- Sukses, Admin --> D
    F -- Sukses, Karyawan --> E

    C -.-> G[Register = akun Karyawan]
    G --> E

    %% ----- Alur Karyawan -----
    E -->|"Belum ada gerobak"| H[Ambil Gerobak]
    H --> H1{Isi produk & jumlah}
    H1 --> H2[Gerobak aktif dibuat<br/>stok gudang berkurang<br/>log: ambil_gerobak]
    H2 --> E

    E -->|"Sedang bawa gerobak"| I[Kembalikan Gerobak]
    I --> I1[Input sisa retur<br/>live preview keuntungan]
    I1 --> I2[Stok sisa kembali ke gudang<br/>bagi hasil 20% dihitung sistem<br/>rincian per produk disimpan<br/>log: retur_gerobak]
    I2 --> J[Komisi & Bagian Hasil]
    J --> J1[Lihat rincian transparan per produk]
    J --> J2[Ambil Penghasilan<br/>pending &gt; saldo &gt; paid<br/>log: ambil_komisi]
    J2 --> E

    %% ----- Alur Admin -----
    D --> K[Line chart penjualan 7 hari per karyawan]
    D --> L[Monitoring Gerobak]
    L --> L1[Tab: Sedang Dibawa<br/>siapa bawa apa & berapa jumlah]
    L --> L2[Tab: Dikembalikan<br/>siapa kembalikan apa & sisa retur]
    L1 --> L3[Detail Gerobak + hasil bagi]
    L2 --> L3
    D --> M[CRUD Produk<br/>log: tambah/edit/hapus_produk]
    D --> N[Kelola Pengguna<br/>log: tambah/hapus_user]
    D --> O[Log Aktivitas<br/>read-only, tanpa CRUD]
```

## Penjelasan Ringkas

1. **Login / Register** — `/login` dan `/register`. Registrasi publik selalu membuat akun ber-role
   `employee`. Redirect otomatis sesuai role.
2. **Dashboard Karyawan** — menampilkan status gerobak, isi gerobak aktif (produk, jumlah, nilai),
   komisi terakhir, dan daftar produk.
3. **Ambil Gerobak** — wajib tanpa gerobak aktif. Pilih produk + jumlah; sistem memvalidasi stok
   (row lock), memotong stok, dan mencatat log `ambil_gerobak`.
4. **Kembalikan Gerobak** — input jumlah sisa (retur). Sistem menghitung `terjual = diambil − sisa`,
   mengembalikan sisa ke stok, menghitung keuntungan & bagian karyawan, menyimpan rincian per produk
   (`commission_items`), dan mencatat log `retur_gerobak`.
5. **Komisi & Bagian Hasil** — riwayat komisi dengan rincian transparan per produk (accordion),
   rumus perhitungan, dan tombol "Ambil Penghasilan" (pending → saldo, log `ambil_komisi`).
6. **Dashboard Admin** — statistik umum + line chart penjualan per karyawan 7 hari terakhir
   (multi-line, filter per karyawan).
7. **Monitoring Gerobak** — read-only. Tab "Sedang Dibawa" menampilkan siapa membawa produk apa
   beserta jumlah; tab "Dikembalikan" menampilkan siapa mengembalikan apa, jumlah terjual, dan
   sisa retur yang kembali ke gudang.
8. **Log Aktivitas** — read-only (tidak ada tombol tambah/ubah/hapus). Filter berdasarkan kata kunci,
   jenis aksi, dan rentang tanggal. Data diisi otomatis oleh observer & event.
9. **Logout** — mencatat log `logout` lalu kembali ke halaman login.

## Daftar Aksi pada Log Aktivitas

| action | Dipicu oleh |
|--------|-------------|
| `login` / `logout` | Event auth (login & logout) |
| `register` | Registrasi publik |
| `ambil_gerobak` | CartController::ambilGerobak |
| `retur_gerobak` | CartObserver (status → returned) |
| `ambil_komisi` | CommissionObserver (status → paid) |
| `tambah_produk` / `edit_produk` / `hapus_produk` | ProductObserver |
| `tambah_user` / `hapus_user` | UserObserver |
