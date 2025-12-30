<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * 1. INDEX: Menampilkan Halaman Keranjang
     * Mengambil semua item yang dimasukkan user ke keranjang dan menghitung total harga.
     */
    public function index()
    {
        // Ambil data keranjang milik User yang sedang login
        // Menggunakan with('product') untuk Eager Loading (optimasi query) agar data produk ikut terambil
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();
        
        // Menghitung Grand Total (Sum dari kolom 'subtotal' semua item)
        $total = $cartItems->sum('subtotal');

        return view('cart', compact('cartItems', 'total'));
    }

    /**
     * 2. STORE: Menambahkan Produk ke Keranjang
     * Logika:
     * - Cek stok produk.
     * - Cek apakah produk SUDAH ADA di keranjang user?
     * - Jika YA: Tambahkan quantity-nya saja (Update).
     * - Jika TIDAK: Buat baris baru di keranjang (Create).
     */
    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);

        // Validasi Stok Awal
        // Pastikan stok cukup untuk permintaan pertama ini
        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        // Cek apakah user ini sudah pernah memasukkan produk yang sama ke keranjang?
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            // SKENARIO A: Produk sudah ada, kita update jumlahnya
            $newQuantity = $cartItem->quantity + $request->quantity;

            // Validasi Stok Kedua
            // Pastikan stok cukup untuk menampung (jumlah lama + jumlah baru)
            if ($product->stock < $newQuantity) {
                return back()->with('error', 'Stok tidak cukup untuk menambah jumlah ini.');
            }

            // Update data yang sudah ada
            $cartItem->update([
                'quantity' => $newQuantity,
                'subtotal' => $product->price * $newQuantity // Hitung ulang subtotal
            ]);
        } else {
            // SKENARIO B: Produk belum ada, buat baru
            Cart::create([
                'user_id'    => Auth::id(),
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity,
                'subtotal'   => $product->price * $request->quantity
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * 3. UPDATE: Mengubah Jumlah (Quantity) di Halaman Keranjang
     * Biasanya dipanggil saat user menekan tombol (+) atau (-) di halaman cart.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Cari item keranjang milik user ini
        $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();

        // Validasi Stok
        // Sebelum update, pastikan stok produk di gudang mencukupi permintaan baru
        if ($cartItem->product->stock < $request->quantity) {
            return back()->with('error', 'Stok produk tidak mencukupi permintaan Anda.');
        }

        // Update quantity dan hitung ulang subtotal
        $cartItem->update([
            'quantity' => $request->quantity,
            'subtotal' => $cartItem->product->price * $request->quantity
        ]);

        return back()->with('success', 'Jumlah produk diperbarui.');
    }

    /**
     * 4. DESTROY: Menghapus Item dari Keranjang
     */
    public function destroy($id)
    {
        // Pastikan hanya menghapus item milik user yang sedang login (Security)
        $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->first();

        if ($cartItem) {
            $cartItem->delete();
            return back()->with('success', 'Produk dihapus dari keranjang.');
        }

        return back()->with('error', 'Produk tidak ditemukan.');
    }
}