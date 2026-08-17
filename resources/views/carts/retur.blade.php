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
                                               class="form-control form-control-sm sisa-input" required
                                               data-ambil="{{ $item->qty_ambil }}"
                                               data-jual="{{ $item->harga_jual }}"
                                               data-modal="{{ $item->harga_modal }}">
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

                {{-- Live preview estimasi saat mengisi sisa --}}
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <div class="border rounded p-2 text-center bg-light">
                            <div class="text-muted small">Barang Terjual (est.)</div>
                            <div class="fw-bold" id="preview-terjual">0</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-2 text-center bg-light">
                            <div class="text-muted small">Keuntungan (est.)</div>
                            <div class="fw-bold" id="preview-untung">Rp0</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-2 text-center bg-success-subtle">
                            <div class="text-muted small">Bagian Anda 20% (est.)</div>
                            <div class="fw-bold text-success" id="preview-bagian">Rp0</div>
                        </div>
                    </div>
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

@push('scripts')
<script>
    // Live preview estimasi keuntungan & bagian 20% saat karyawan mengisi sisa retur
    const persen = 0.20;
    const fmtRupiah = (v) => 'Rp' + Number(v).toLocaleString('id-ID');

    function hitungPreview() {
        let terjual = 0, untung = 0;
        document.querySelectorAll('.sisa-input').forEach((input) => {
            const sisa = Math.min(Math.max(parseInt(input.value || '0', 10), 0), parseInt(input.dataset.ambil, 10));
            const qtyTerjual = parseInt(input.dataset.ambil, 10) - sisa;
            const untungItem = qtyTerjual * (parseFloat(input.dataset.jual) - parseFloat(input.dataset.modal));
            terjual += qtyTerjual;
            untung += untungItem;
        });
        document.getElementById('preview-terjual').textContent = terjual;
        document.getElementById('preview-untung').textContent = fmtRupiah(untung);
        document.getElementById('preview-bagian').textContent = fmtRupiah(untung * persen);
    }

    document.querySelectorAll('.sisa-input').forEach((input) => {
        input.addEventListener('input', hitungPreview);
    });
    hitungPreview();
</script>
@endpush