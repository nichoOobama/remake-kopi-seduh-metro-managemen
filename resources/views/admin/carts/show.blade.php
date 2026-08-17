@extends('layouts.app')

@section('title', 'Detail Gerobak — CoffeePaste')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-cart"></i> Detail Gerobak — {{ $gerobak->user->name }}</h1>
        <a href="{{ route('admin.gerobak.index', ['status' => $gerobak->status]) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Info gerobak --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-muted small">Status</div>
                <span class="badge text-bg-{{ $gerobak->status === 'active' ? 'warning' : 'success' }} fs-6">
                    {{ $gerobak->status === 'active' ? 'Sedang Dibawa' : 'Dikembalikan' }}
                </span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-muted small">Diambil</div>
                <div class="fw-bold">{{ $gerobak->taken_at?->format('d M Y H:i') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-muted small">Dikembalikan</div>
                <div class="fw-bold">{{ $gerobak->returned_at?->format('d M Y H:i') ?? '—' }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-muted small">Total Bungkus</div>
                <div class="fw-bold">{{ $gerobak->items->sum('qty_ambil') }}</div>
            </div>
        </div>
    </div>

    {{-- Detail item --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-bold"><i class="bi bi-box-seam"></i> Rincian Barang</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th class="text-center">Diambil</th>
                            @if ($gerobak->status === 'returned')
                                <th class="text-center">Terjual</th>
                                <th class="text-center">Sisa Retur</th>
                            @endif
                            <th class="text-end">Harga Jual</th>
                            <th class="text-end">Harga Modal</th>
                            @if ($gerobak->status === 'returned')
                                <th class="text-end">Keuntungan</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gerobak->items as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->product->name }}</td>
                                <td class="text-center">{{ $item->qty_ambil }}</td>
                                @if ($gerobak->status === 'returned')
                                    <td class="text-center">{{ $item->qty_terjual }}</td>
                                    <td class="text-center">{{ $item->qty_sisa }}</td>
                                @endif
                                <td class="text-end">Rp{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                <td class="text-end">Rp{{ number_format($item->harga_modal, 0, ',', '.') }}</td>
                                @if ($gerobak->status === 'returned')
                                    <td class="text-end fw-semibold">Rp{{ number_format($item->keuntungan(), 0, ',', '.') }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Ringkasan komisi bila sudah dikembalikan --}}
    @if ($gerobak->status === 'returned' && $komisi->exists)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold"><i class="bi bi-cash-coin"></i> Hasil Bagi</div>
            <div class="card-body">
                <div class="row text-center g-3">
                    <div class="col-md-4">
                        <div class="text-muted small">Total Penjualan</div>
                        <div class="fs-5 fw-bold">Rp{{ number_format($komisi->total_penjualan, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Total Keuntungan</div>
                        <div class="fs-5 fw-bold">Rp{{ number_format($komisi->total_untung, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Bagian Karyawan (20%)</div>
                        <div class="fs-5 fw-bold text-success">Rp{{ number_format($komisi->upah_20persen, 0, ',', '.') }}</div>
                        <span class="badge text-bg-{{ $komisi->status === 'paid' ? 'success' : 'secondary' }}">
                            {{ $komisi->status === 'paid' ? 'Sudah diambil' : 'Menunggu diambil' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection