<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * 1. INDEX: Menampilkan daftar Kategori
     */
    public function index()
    {
        // Mengambil data kategori terbaru dengan pagination 10 item per halaman.
        $categories = Category::latest()->paginate(10);
        
        return view('categories.category', compact('categories'));
    }

    /**
     * 2. CREATE: Menampilkan form tambah kategori
     */
    public function create()
    {
        return view('categories.createCategory');
    }

    /**
     * 3. STORE: Menyimpan kategori baru ke database
     */
    public function store(Request $request)
    {
        // Validasi Input
        $validated = $request->validate([
            // unique:categories,name -> Mencegah duplikasi nama kategori
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        // Simpan data
        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * 4. EDIT: Menampilkan form edit kategori
     * Menggunakan Route Model Binding (Category $category) untuk mengambil data otomatis berdasarkan ID.
     */
    public function edit(Category $category)
    {
        return view('categories.editCategory', compact('category'));
    }

    /**
     * 5. UPDATE: Memperbarui data kategori
     */
    public function update(Request $request, Category $category)
    {
        // Validasi Input
        $validated = $request->validate([
            // PENTING: unique:categories,name,' . $category->id
            // Kita harus mengecualikan ID kategori ini sendiri dari pengecekan unik.
            // Jika tidak, sistem akan mengira nama ini duplikat dengan dirinya sendiri.
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * 6. DESTROY: Menghapus kategori dengan Pengecekan Relasi
     */
    public function destroy(Category $category)
    {
        // SAFETY CHECK: Data Integrity
        // Sebelum menghapus, cek apakah ada produk yang terdaftar di kategori ini.
        // Jika ada, batalkan penghapusan agar produk tidak kehilangan kategorinya (Orphaned Data).
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih memiliki produk.');
        }

        // Jika aman (kosong), hapus kategori.
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}