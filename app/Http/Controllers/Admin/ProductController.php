<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * CRUD produk kopi (khusus admin).
 * Foto bersifat opsional; bila di-upload disimpan di storage/app/public/products.
 */
class ProductController extends Controller
{
    /** Daftar semua produk. */
    public function index(Request $request)
    {
        $keyword = $request->query('q');

        $produk = Product::query()
            ->when($keyword, fn ($q) => $q->where('name', 'like', "%{$keyword}%"))
            ->orderBy('name')
            ->paginate(10);

        return view('admin.products.index', compact('produk', 'keyword'));
    }

    /** Form tambah produk. */
    public function create()
    {
        return view('admin.products.form');
    }

    /** Simpan produk baru. */
    public function store(Request $request)
    {
        $data = $this->validateProduk($request);
        $data['foto'] = $this->simpanFoto($request);

        Product::create($data);

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /** Form edit produk. */
    public function edit(Product $produk)
    {
        return view('admin.products.form', compact('produk'));
    }

    /** Update produk. */
    public function update(Request $request, Product $produk)
    {
        $data = $this->validateProduk($request);

        // Ganti foto bila ada upload baru, hapus yang lama
        if ($request->hasFile('foto')) {
            if ($produk->foto) {
                Storage::disk('public')->delete($produk->foto);
            }
            $data['foto'] = $this->simpanFoto($request);
        }

        $produk->update($data);

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /** Hapus produk (beserta fotonya). */
    public function destroy(Product $produk)
    {
        if ($produk->foto) {
            Storage::disk('public')->delete($produk->foto);
        }
        $produk->delete();

        return redirect()->route('admin.produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /** Aturan validasi bersama untuk store & update. */
    private function validateProduk(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'harga_modal' => ['required', 'numeric', 'min:0'],
            'harga_jual' => ['required', 'numeric', 'min:0', 'gt:harga_modal'],
            'stok' => ['required', 'integer', 'min:0'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);
    }

    /** Simpan foto upload; diakses via public/storage (storage:link). */
    private function simpanFoto(Request $request): ?string
    {
        if (! $request->hasFile('foto')) {
            return null;
        }

        return $request->file('foto')->store('products', 'public');
    }
}