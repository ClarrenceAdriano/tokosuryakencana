<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Transaction_detail;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * 1. INDEX: Riwayat Transaksi
     * Menampilkan daftar transaksi milik user yang sedang login.
     */
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->latest() // Urutkan dari yang terbaru
            ->paginate(10);

        return view('transactions.transaction', compact('transactions'));
    }

    /**
     * 2. CREATE: Halaman Checkout / Pembayaran
     * Menyiapkan data keranjang, total harga, dan alamat sebelum user menekan "Bayar".
     */
    public function create()
    {
        $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();

        // Cek apakah keranjang kosong? Jika ya, tendang balik ke katalog.
        if ($cartItems->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Keranjang kosong.');
        }

        $total = $cartItems->sum('subtotal');
        $addresses = Address::where('user_id', Auth::id())->get();

        return view('payment', compact('cartItems', 'total', 'addresses'));
    }

    /**
     * 3. STORE: Proses Transaksi (CORE LOGIC)
     */
    public function store(Request $request)
    {
        // A. Validasi Input
        $request->validate([
            'address_id'      => 'required|exists:addresses,id',
            'payment_method'  => 'required|string|in:transfer,cod,ewallet',
        ]);

        $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('products.index');
        }

        try {
            // B. MULAI DATABASE TRANSACTION
            // Artinya: Semua query di bawah ini dianggap satu paket.
            // Kalau ada error di tengah jalan, database akan di-rollback (dikembalikan ke kondisi awal).
            DB::beginTransaction();

            $totalPay = $cartItems->sum('subtotal');

            // 1. Buat Header Transaksi
            $transaction = Transaction::create([
                'user_id'        => Auth::id(),
                'address_id'     => $request->address_id,
                'total'          => $totalPay,
                'payment_method' => $request->payment_method,
                'status'         => 'pending',
            ]);

            // 2. Proses Setiap Item di Keranjang
            foreach ($cartItems as $item) {
                // PENTING: lockForUpdate()
                // Mencegah Race Condition. Saat kode ini jalan, user lain TIDAK BISA
                // membeli produk ini sampai transaksi ini selesai (commit/rollback).
                $product = Product::lockForUpdate()->find($item->product_id);

                // Cek Stok Real-time (Setelah dilock)
                if (!$product || $product->stock < $item->quantity) {
                    // Jika stok habis saat mau bayar, batalkan semua proses!
                    DB::rollBack();
                    return back()->with('error', 'Stok produk ' . $item->product->name . ' tidak mencukupi.');
                }

                // Simpan Detail Transaksi
                Transaction_detail::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $item->product_id,
                    'quantity'       => $item->quantity,
                    'subtotal'       => $item->subtotal,
                ]);

                // Kurangi Stok
                $product->decrement('stock', $item->quantity);
            }

            // 3. Kosongkan Keranjang User
            Cart::where('user_id', Auth::id())->delete();

            // C. COMMIT DATABASE
            // Simpan semua perubahan secara permanen.
            DB::commit();

            return redirect()->route('transaction.show', $transaction->id)
                ->with('success', 'Transaksi berhasil dibuat!');

        } catch (\Exception $e) {
            // D. ROLLBACK (Jika ada error sistem)
            // Batalkan semua query di atas (transaksi batal dibuat, stok dikembalikan, keranjang tidak jadi dihapus).
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * 4. SHOW: Detail Transaksi (Nota)
     */
    public function show(Transaction $transaction)
    {
        // SECURITY CHECK:
        // Pastikan yang melihat nota ini adalah PEMILIK transaksi atau ADMIN.
        // Jangan sampai User A mengintip belanjaan User B.
        if ($transaction->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Load detail produk dan user pembeli
        $transaction->load(['transaction_details.product', 'user']);

        return view('transactions.detailTransaction', compact('transaction'));
    }

    /**
     * 5. UPDATE STATUS: Mengubah status transaksi
     * Logika sederhana untuk simulasi flow: Pending -> Paid -> Shipped -> Done
     */
    public function updateStatus(Request $request, Transaction $transaction)
    {
        // Security Check
        if ($transaction->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // State Machine sederhana
        $nextStatus = match ($transaction->status) {
            'pending' => 'paid',     // User bayar
            'paid'    => 'shipped',  // Admin kirim barang
            'shipped' => 'done',     // Barang sampai
            default   => null,
        };

        if ($nextStatus) {
            $transaction->update([
                'status' => $nextStatus
            ]);

            return back()->with('success', 'Status transaksi berhasil diperbarui menjadi ' . strtoupper($nextStatus));
        }

        return back()->with('error', 'Status tidak dapat diperbarui.');
    }
}