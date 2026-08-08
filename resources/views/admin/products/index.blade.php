@extends('layouts.app')

@section('title', 'Kelola Produk — CoffeePaste')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-box-seam"></i> Kelola Produk (CRUD)</h1>
        <a href="{{ route('admin.produk.create') }}" class="btn btn-warning fw-bold">
            <i class="bi bi-plus-lg"></i> Tambah Produk
        </a>
    </div>

    {{-- Pencarian --}}
    <form method="GET" class="mb-3">
        <div class="input-group" style="max-width: 360px;">
            <input type="text" name="q" value="{{ $keyword }}" class="form-control" placeholder="Cari produk...">
            <button class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th class="text-end">Harga Modal</th>
                            <th class="text-end">Harga Jual</th>
                            <th class="text-center">Stok</th>
                            <th class="text-end">Potensi Untung</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produk as $p)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($p->foto)
                                            <img src="{{ asset('storage/' . $p->foto) }}" width="40" height="40"
                                                 class="rounded object-fit-cover" alt="">
                                        @else
                                            <div class="bg-secondary-subtle rounded d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                <i class="bi bi-cup text-secondary"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $p->name }}</div>
                                            <small class="text-muted">{{ Str::limit($p->description, 50) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">Rp{{ number_format($p->harga_modal, 0, ',', '.') }}</td>
                                <td class="text-end">Rp{{ number_format($p->harga_jual, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge text-bg-{{ $p->stok > 0 ? 'success' : 'danger' }}">{{ $p->stok }}</span>
                                </td>
                                <td class="text-end fw-semibold text-success">
                                    Rp{{ number_format(($p->harga_jual - $p->harga_modal) * $p->stok, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.produk.edit', $p) }}" class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.produk.destroy', $p) }}" class="m-0"
                                              onsubmit="return confirm('Hapus produk {{ $p->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada produk ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $produk->links() }}
        </div>
    </div>
@endsection