@extends('layouts.app')

@section('title', isset($produk) ? 'Edit Produk — CoffeePaste' : 'Tambah Produk — CoffeePaste')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-{{ isset($produk) ? 'pencil' : 'plus-circle' }}"></i>
            {{ isset($produk) ? 'Edit Produk' : 'Tambah Produk' }}
        </h1>
        <a href="{{ route('admin.produk.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    {{-- Form ini dipakai untuk store (POST) maupun update (PUT) --}}
    <form method="POST"
          action="{{ isset($produk) ? route('admin.produk.update', $produk) : route('admin.produk.store') }}"
          enctype="multipart/form-data"
          class="card border-0 shadow-sm">
        @csrf
        @if (isset($produk))
            @method('PUT')
        @endif

        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                           value="{{ old('name', $produk->name ?? '') }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Stok <span class="text-danger">*</span></label>
                    <input type="number" name="stok" min="0" class="form-control"
                           value="{{ old('stok', $produk->stok ?? 0) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Harga Modal / Beli (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="harga_modal" min="0" step="0.01" class="form-control"
                           value="{{ old('harga_modal', $produk->harga_modal ?? '') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Harga Jual (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="harga_jual" min="0" step="0.01" class="form-control"
                           value="{{ old('harga_jual', $produk->harga_jual ?? '') }}" required>
                    <small class="text-muted">Harus lebih besar dari harga modal (agar ada keuntungan bagi hasil).</small>
                </div>

                <div class="col-12">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $produk->description ?? '') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Foto Produk (opsional)</label>
                    <input type="file" name="foto" accept="image/*" class="form-control">

                    @if (isset($produk) && $produk->foto)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $produk->foto) }}" class="rounded border" width="80" height="80"
                                 style="object-fit: cover;">
                            <small class="text-muted ms-2">Foto lama (upload baru untuk mengganti)</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-footer bg-white d-flex justify-content-end gap-2">
            <a href="{{ route('admin.produk.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button class="btn btn-warning fw-bold">
                <i class="bi bi-check-lg"></i> {{ isset($produk) ? 'Simpan Perubahan' : 'Simpan Produk' }}
            </button>
        </div>
    </form>
@endsection