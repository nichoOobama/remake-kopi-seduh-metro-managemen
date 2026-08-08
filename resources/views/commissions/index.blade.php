@extends('layouts.app')

@section('title', 'Komisi & Bagian Hasil — CoffeePaste')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-cash-coin"></i> Komisi &amp; Bagian Hasil</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Dashboard</a>
    </div>

    {{-- Ringkasan saldo --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-muted small">Saldo Saat Ini</div>
                <div class="fs-3 fw-bold text-success">Rp{{ number_format(auth()->user()->balance, 0, ',', '.') }}</div>
                <div class="text-muted small">sudah diambil dari komisi</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-muted small">Komisi Menunggu</div>
                <div class="fs-3 fw-bold text-warning">Rp{{ number_format($totalPending, 0, ',', '.') }}</div>
                <div class="text-muted small">belum diambil</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm text-center p-3">
                <div class="text-muted small">Total Komisi Diterima</div>
                <div class="fs-3 fw-bold">Rp{{ number_format($totalDiterima, 0, ',', '.') }}</div>
                <div class="text-muted small">sepanjang waktu</div>
            </div>
        </div>
    </div>

    {{-- Riwayat komisi per gerobak --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-bold"><i class="bi bi-receipt"></i> Riwayat Bagian Hasil</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Dikembalikan</th>
                            <th class="text-end">Penjualan</th>
                            <th class="text-end">Modal</th>
                            <th class="text-end">Keuntungan</th>
                            <th class="text-end">Bagian Anda (20%)</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($komisi as $k)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $k->cart->returned_at?->format('d M Y H:i') }}</td>
                                <td class="text-end">Rp{{ number_format($k->total_penjualan, 0, ',', '.') }}</td>
                                <td class="text-end">Rp{{ number_format($k->total_modal, 0, ',', '.') }}</td>
                                <td class="text-end fw-semibold">Rp{{ number_format($k->total_untung, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-success">Rp{{ number_format($k->upah_20persen, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge text-bg-{{ $k->status === 'paid' ? 'success' : 'secondary' }}">
                                        {{ $k->status === 'paid' ? 'Diambil' : 'Menunggu' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($k->status === 'pending')
                                        {{-- Ambil penghasilan: komisi masuk ke saldo --}}
                                        <form method="POST" action="{{ route('komisi.ambil', $k) }}" class="m-0"
                                              onsubmit="return confirm('Ambil penghasilan Rp{{ number_format($k->upah_20persen, 0, ',', '.') }}?')">
                                            @csrf
                                            <button class="btn btn-sm btn-success"><i class="bi bi-wallet2"></i> Ambil Penghasilan</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">{{ $k->paid_at?->format('d M Y H:i') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Belum ada riwayat komisi. Kembalikan gerobak untuk menghitung bagian hasil Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection