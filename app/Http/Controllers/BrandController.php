<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * 1. INDEX: Menampilkan daftar Brand
     */
    public function index()
    {
        // Mengambil data brand terbaru dan membatasi 10 data per halaman (Pagination)
        $brands = Brand::latest()->paginate(10);
        
        return view('brands.brand', compact('brands'));
    }

    /**
     * 2. CREATE: Form tambah brand
     */
    public function create()
    {
        return view('brands.createBrand');
    }

    /**
     * 3. STORE: Menyimpan brand baru
     */
    public function store(Request $request)
    {
        // Validasi Input
        $validated = $request->validate([
            // 'unique:brands,name' -> Memastikan tidak ada nama brand yang sama di database
            'name' => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string',
        ]);

        // Simpan ke database
        Brand::create($validated);

        return redirect()->route('brands.index')
            ->with('success', 'Brand berhasil ditambahkan.');
    }

    /**
     * 4. EDIT: Form edit brand
     * Menggunakan Route Model Binding (Brand $brand) otomatis mencari data berdasarkan ID.
     */
    public function edit(Brand $brand)
    {
        return view('brands.editBrand', compact('brand'));
    }

    /**
     * 5. UPDATE: Memperbarui data brand
     */
    public function update(Request $request, Brand $brand)
    {
        // Validasi Input
        $validated = $request->validate([
            // PENTING: unique:brands,name, . $brand->id
            // Artinya: Nama harus unik, KECUALI untuk brand yang sedang diedit ini sendiri.
            // Jika tidak dikecualikan, validasi akan gagal karena dianggap duplikat dengan nama lamanya.
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
        ]);

        $brand->update($validated);

        return redirect()->route('brands.index')
            ->with('success', 'Brand berhasil diperbarui.');
    }

    /**
     * 6. DESTROY: Menghapus brand (Dengan Safety Check)
     */
    public function destroy(Brand $brand)
    {
        // Sebelum menghapus brand, cek apakah masih ada produk yang menggunakan brand ini.
        // Jika ada, batalkan penghapusan untuk menjaga integritas data (biar produk tidak kehilangan induknya).
        if ($brand->products()->count() > 0) {
            return back()->with('error', 'Brand tidak bisa dihapus karena masih memiliki produk.');
        }

        // Jika aman (tidak ada produk), hapus brand.
        $brand->delete();

        return redirect()->route('brands.index')
            ->with('success', 'Brand berhasil dihapus.');
    }
}