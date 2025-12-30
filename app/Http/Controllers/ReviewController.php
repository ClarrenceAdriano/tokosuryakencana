<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * 1. STORE: Menyimpan Ulasan Baru
     */
    public function store(Request $request)
    {
        // A. Validasi Input
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id', // Wajib valid
            'product_id'     => 'required|exists:products,id',     // Wajib valid
            'rate'           => 'required|integer|min:1|max:5',    // Bintang 1-5
            'comment'        => 'required|string|max:1000',        // Komentar
        ]);

        // B. LOGIKA PENGECEKAN DUPLIKASI (Anti-Spam)
        // Cek database: Apakah di TRANSAKSI INI, untuk PRODUK INI, review sudah ada?
        $existingReview = Review::where('transaction_id', $request->transaction_id)
            ->where('product_id', $request->product_id)
            ->first();

        // Jika sudah ada, tolak permintaan.
        if ($existingReview) {
            return back()->with('error', 'Anda sudah mengulas produk pada transaksi ini.');
        }

        // C. Simpan Review
        Review::create([
            'user_id'        => Auth::id(),                 // User yang sedang login
            'transaction_id' => $request->transaction_id,   // Bukti transaksi
            'product_id'     => $request->product_id,
            'rate'           => $request->rate,
            'comment'        => $request->comment,
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }

    /**
     * 2. UPDATE: Mengedit Ulasan
     * User mungkin ingin mengubah bintang atau komentar mereka.
     */
    public function update(Request $request, $id)
    {
        // Validasi input edit
        $request->validate([
            'rate'    => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        // Cari data review, jika tidak ketemu tampilkan 404
        $review = Review::findOrFail($id);

        // SECURITY CHECK (Authorization):
        // Pastikan yang mau ngedit adalah PEMILIK review itu sendiri.
        // Jangan sampai User A mengedit review milik User B.
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.'); // Akses ditolak
        }

        // Update data
        $review->update([
            'rate'    => $request->rate,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Ulasan berhasil diperbarui.');
    }

    /**
     * 3. DESTROY: Menghapus Ulasan
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        // SECURITY CHECK (Authorization):
        // Pastikan hanya pemilik review yang boleh menghapus.
        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}