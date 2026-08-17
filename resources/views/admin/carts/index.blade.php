@extends('layouts.app')

@section('title', 'Monitoring Gerobak — CoffeePaste')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-cart-check"></i> Monitoring Gerobak Karyawan</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Dashboard</a>
    </div>

    {{-- Ringkasan --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-cart-fill fs-3 text-warning"></i>
                <div class="fs-4 fw-bold">{{ $totalAktif }}</div>
                <div class="text-muted small">Gerobak Aktif</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-arrow-return-left fs-3 text-success"></i>
                <div class="fs-4 fw-bold">{{ $totalDikembalikan }}</div>
                <div class="text-muted small">Gerobak Dikembalikan</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-boxes fs-3 text-info"></i>
                <div class="fs-4 fw-bold">{{ number_format($totalTerjual, 0, ',', '.') }}</div>
                <div class="text-muted small">Total Barang Terjual</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center p-3">
                <i class="bi bi-box-arrow-in-down fs-3 text-secondary"></i>
                <div class="fs-4 fw-bold">{{ number_format($totalReturStok, 0, ',', '.') }}</div>
                <div class="text-muted small">Barang Retur ke Gudang</div>
            </div>
        </div>
    </div>

    {{-- Filter: tab status + pencarian --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <ul class="nav nav-pills gap-1">
                    <li class="nav-item">
                        <a class="nav-link {{ $status === 'active' ? 'active' : 'text-body' }}" href="{{ route('admin.gerobak.index', ['status' => 'active', 'q' => $cari]) }}">
                            Sedang Dibawa ({{ $totalAktif }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $status === 'returned' ? 'active' : 'text-body' }}" href="{{ route('admin.gerobak.index', ['status' => 'returned', 'q' => $cari]) }}">
                            Dikembalikan ({{ $totalDikembalikan }})
                        </a>
                    </li>
                </ul>

                <form method="GET" class="d-flex gap-2">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <input type="text" name="q" value="{{ $cari }}" class="form-control form-control-sm"
                           placeholder="Cari nama karyawan..." style="width: 220px;">
                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Cari</button>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Karyawan</th>
                            <th>{{ $status === 'active' ? 'Diambil' : 'Dikembalikan' }}</th>
                            <th class="text-center">Jenis</th>
                            <th class="text-center">Diambil</th>
                            @if ($status === 'returned')
                                <th class="text-center">Terjual</th>
                                <th class="text-center">Sisa Retur</th>
                                <th class="text-end">Penjualan</th>
                                <th class="text-end">Keuntungan</th>
                                <th class="text-end">Bagian (20%)</th>
                            @endif
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($gerobak as $g)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $g->user->name }}</span>
                                    <small class="d-block text-muted">{{ $g->user->email }}</small>
                                </td>
                                <td>
                                    @if ($status === 'active')
                                        {{ $g->taken_at?->format('d M Y H:i') }}
                                    @else
                                        {{ $g->returned_at?->format('d M Y H:i') }}
                                        <small class="d-block text-muted">diambil {{ $g->taken_at?->format('d M Y H:i') }}</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $g->items->count() }}</td>
                                <td class="text-center fw-semibold">{{ $g->items->sum('qty_ambil') }}</td>
                                @if ($status === 'returned')
                                    <td class="text-center">{{ $g->items->sum('qty_terjual') }}</td>
                                    <td class="text-center">{{ $g->items->sum('qty_sisa') }}</td>
                                    <td class="text-end">Rp{{ number_format($g->commission?->total_penjualan ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp{{ number_format($g->commission?->total_untung ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end fw-semibold text-success">Rp{{ number_format($g->commission?->upah_20persen ?? 0, 0, ',', '.') }}</td>
                                @endif
                                <td class="text-center">
                                    <a href="{{ route('admin.gerobak.show', $g) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    Tidak ada gerobak {{ $status === 'active' ? 'aktif' : 'dikembalikan' }}.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($gerobak->hasPages())
            <div class="card-footer bg-white">
                {{ $gerobak->links() }}
            </div>
        @endif
    </div>
@endsection