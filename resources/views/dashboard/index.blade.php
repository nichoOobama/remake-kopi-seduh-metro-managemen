@extends('layouts.app')

@section('title', 'Dashboard — CoffeePaste')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Halo, {{ auth()->user()->name }}!</h1>

        {{-- Aksi gerobak: ambil (bila belum punya) / kembalikan (bila sedang bawa) --}}
        @if ($gerobakAktif)
            <a href="{{ route('gerobak.retur') }}" class="btn btn-warning fw-bold">
                <i class="bi bi-arrow-return-left"></i> Kembalikan Gerobak
            </a>
        @else
            <a href="{{ route('gerobak.ambil') }}" class="btn btn-success fw-bold">
                <i class="bi bi-cart-plus"></i> Ambil Gerobak
            </a>
        @endif
    </div>

    {{-- Status gerobak --}}
    @if ($gerobakAktif)
        <div class="alert alert-warning d-flex align-items-center gap-2">
            <i class="bi bi-cart-fill fs-4"></i>
            <div>
                <strong>Gerobak aktif</strong> sejak {{ $gerobakAktif->taken_at->format('d M Y H:i') }} —
                {{ $gerobakAktif->items->count() }} jenis produk, total
                {{ $gerobakAktif->items->sum('qty_ambil') }} bungkus.
                <a href="{{ route('gerobak.retur') }}" class="alert-link">Kembalikan sekarang</a>
            </div>
        </div>
    @else
        <div class="alert alert-info d-flex align-items-center gap-2">
            <i class="bi bi-info-circle-fill"></i>
            <div>
                Belum ada gerobak aktif. Klik <strong>Ambil Gerobak</strong> untuk mulai berjualan.
                Setiap karyawan maksimal membawa <strong>1 gerobak</strong>.
            </div>
        </div>
    @endif

    {{-- Ringkasan isi gerobak aktif --}}
    @if ($gerobakAktif && $gerobakAktif->items->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-bold"><i class="bi bi-cart3"></i> Isi Gerobak Anda</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Jumlah Dibawa</th>
                                <th class="text-end">Harga Jual</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($gerobakAktif->items as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->product->name }}</td>
                                    <td class="text-center">{{ $item->qty_ambil }}</td>
                                    <td class="text-end">Rp{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp{{ number_format($item->qty_ambil * $item->harga_jual, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-semibold">Total Nilai Jual</td>
                                <td class="text-end fw-bold">Rp{{ number_format($gerobakAktif->items->sum(fn ($i) => $i->qty_ambil * $i->harga_jual), 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Informasi komisi terakhir --}}
    @if ($komisiTerakhir)
        <div class="alert alert-success d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-cash-coin"></i>
                Bagian hasil terakhir: <strong>Rp{{ number_format($komisiTerakhir->upah_20persen, 0, ',', '.') }}</strong>
                (keuntungan Rp{{ number_format($komisiTerakhir->total_untung, 0, ',', '.') }})
                — status <span class="badge text-bg-{{ $komisiTerakhir->status === 'paid' ? 'success' : 'secondary' }}">{{ $komisiTerakhir->status }}</span>
            </div>
            <a href="{{ route('komisi.index') }}" class="btn btn-sm btn-outline-success">Lihat semua</a>
        </div>
    @endif

    {{-- Daftar produk yang bisa dijual --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold">
            <i class="bi bi-box-seam"></i> Daftar Produk
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th class="text-end">Harga Jual</th>
                            <th class="text-center">Stok</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produk as $p)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($p->foto)
                                            <img src="{{ asset('storage/' . $p->foto) }}" width="40" height="40"
                                                 class="rounded object-fit-cover" alt="{{ $p->name }}">
                                        @else
                                            <div class="bg-secondary-subtle rounded d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-cup text-secondary"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $p->name }}</div>
                                            <small class="text-muted">{{ Str::limit($p->description, 60) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end fw-semibold">Rp{{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge text-bg-{{ $p->stok > 0 ? 'primary' : 'danger' }}">{{ $p->stok }}</span>
                                </td>
                                <td class="text-center">
                                    {{-- Pilih produk practical: redirect ke ambil gerobak agar tambah langsung --}}
                                    @if ($p->stok > 0 && ! $gerobakAktif)
                                        <a href="{{ route('gerobak.ambil') }}" class="btn btn-sm btn-outline-success">Jual</a>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary" disabled>Tidak tersedia</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada produk.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection