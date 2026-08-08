@extends('layouts.app')

@section('title', 'Kembalikan Gerobak — CoffeePaste')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-arrow-return-left"></i> Kembalikan Gerobak</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Dashboard</a>
    </div>

    <form method="POST" action="{{ route('gerobak.retur.proses') }}">
        @csrf

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <span class="fw-bold">Gerobak diambil: {{ $gerobak->taken_at->format('d M Y H:i') }}</span>
                <span class="text-muted ms-2">(diambil {{ $gerobak->items->count() }} jenis, {{ $gerobak->items->sum('qty_ambil') }} bungkus)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Diambil</th>
                                <th class="text-center">Harga Jual</th>
                                <th class="text-center">Harga Modal</th>
                                <th style="width: 180px;">Sisa (Retur)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($gerobak->items as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->product->name }}</td>
                                    <td class="text-center">{{ $item->qty_ambil }}</td>
                                    <td class="text-center">Rp{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                    <td class="text-center">Rp{{ number_format($item->harga_modal, 0, ',', '.') }}</td>
                                    <td>
                                        {{-- Sisa barang yang TIDAK terjual; sistem menghitung yang terjual = diambil - sisa --}}
                                        <input type="number" name="sisa[{{ $item->id }}]"
                                               min="0" max="{{ $item->qty_ambil }}" value="0"
                                               class="form-control form-control-sm" required>
                                        <small class="text-muted">0 jika semua terjual</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="alert alert-info mb-3 small">
                    <i class="bi bi-lightbulb"></i>
                    Barang sisa akan dikembalikan ke stok gudang (fitur retur otomatis).
                    Komisi Anda dihitung otomatis: <strong>20% dari keuntungan penjualan</strong>
                    (keuntungan = harga jual − harga modal untuk setiap produk yang terjual).
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-warning fw-bold">
                        <i class="bi bi-calculator"></i> Kembalikan &amp; Hitung Bagian Hasil
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection