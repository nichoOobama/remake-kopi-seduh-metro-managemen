@extends('layouts.app')

@section('title', 'Ambil Gerobak — CoffeePaste')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-cart-plus"></i> Ambil Gerobak</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Dashboard</a>
    </div>

    <form method="POST" action="{{ route('gerobak.ambil.proses') }}">
        @csrf

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">
                Pilih produk & jumlah (sisa stok berkurang otomatis di gudang)
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>Produk</th>
                                <th class="text-end">Harga Jual</th>
                                <th class="text-center">Stok Tersedia</th>
                                <th style="width: 160px;">Jumlah Diambil</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($produk as $p)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input"
                                               name="produk[]" value="{{ $p->id }}"
                                               id="produk-{{ $p->id }}" onchange="toggleQty({{ $p->id }}, this.checked)">
                                    </td>
                                    <td>
                                        <label for="produk-{{ $p->id }}" class="fw-semibold">{{ $p->name }}</label>
                                        @if ($p->foto)
                                            <img src="{{ asset('storage/' . $p->foto) }}" width="36" height="36"
                                                 class="rounded ms-2 object-fit-cover" alt="">
                                        @endif
                                    </td>
                                    <td class="text-end">Rp{{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge text-bg-success">{{ $p->stok }}</span>
                                    </td>
                                    <td>
                                        <input type="number" name="qty[{{ $p->id }}]" min="1" max="{{ $p->stok }}"
                                               value="{{ old('qty.' . $p->id, 1) }}" class="form-control form-control-sm"
                                               id="qty-{{ $p->id }}" disabled>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Semua produk stok habis. Hubungi admin.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-end gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                <button class="btn btn-success fw-bold" {{ $produk->isEmpty() ? 'disabled' : '' }}>
                    <i class="bi bi-cart-check"></i> Ambil Gerobak
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    // Aktifkan/nonaktifkan input jumlah mengikuti checkbox produk
    function toggleQty(productId, checked) {
        document.getElementById('qty-' + productId).disabled = !checked;
    }
</script>
@endpush