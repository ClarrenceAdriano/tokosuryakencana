<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * 1. INDEX: Menampilkan Daftar Produk
     * Logika: Menggunakan Eager Loading ('category', 'brand') untuk mencegah N+1 Query Problem.
     * Menggunakan pagination(12) agar halaman rapi dan tidak berat loadingnya.
     */
    public function index()
    {
        $products = Product::with(['category', 'brand'])->latest()->paginate(12);
        return view('products.product', compact('products'));
    }

    /**
     * 2. CREATE: Form Tambah Produk
     * Kita perlu mengirim data 'Categories' dan 'Brands' ke view
     * agar user bisa memilihnya lewat Dropdown (<select>) di form input.
     */
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('products.createProduct', compact('categories', 'brands'));
    }

    /**
     * 3. STORE: Menyimpan Produk Baru ke Database
     */
    public function store(Request $request)
    {
        // A. Validasi Input
        // Pastikan category_id dan brand_id benar-benar ada di tabel referensinya.
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'required|exists:brands,id',
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        // B. Logic Upload Gambar
        // Jika user mengupload file, simpan ke storage/app/public/products
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // C. Simpan ke Database
        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * 4. SHOW: Menampilkan Detail Produk Satu per Satu
     * Logika: Kita load relasi yang lebih dalam (Nested Relationship).
     * 'reviews.user' artinya: Ambil review produk ini, SEKALIGUS ambil data user yang nulis reviewnya.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'reviews.user']);
        return view('products.detailProduct', compact('product'));
    }

    /**
     * 5. EDIT: Form Edit Produk
     * Sama seperti Create, kita butuh data Category dan Brand untuk dropdown.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('products.editProduct', compact('product', 'categories', 'brands'));
    }

    /**
     * 6. UPDATE: Memperbarui Data Produk
     * Bagian terpenting disini adalah logic penggantian (replace) gambar.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id'    => 'required|exists:brands,id',
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'stock'       => 'required|integer|min:0',
            'price'       => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Logic Ganti Gambar:
        if ($request->hasFile('image')) {
            // 1. Cek apakah ada gambar lama? Jika ada, HAPUS dari penyimpanan fisik (biar server gak penuh)
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // 2. Upload gambar baru
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Update data di database
        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * 7. DESTROY: Menghapus Produk
     * Jangan lupa menghapus file gambarnya juga agar tidak jadi file sampah (orphan file).
     */
    public function destroy(Product $product)
    {
        // 1. Hapus file fisik gambar jika ada
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // 2. Hapus data dari database
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}