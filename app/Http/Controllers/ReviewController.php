<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'product_id'     => 'required|exists:products,id',
            'rate'           => 'required|integer|min:1|max:5',
            'comment'        => 'required|string|max:1000',
        ]);

        $existingReview = Review::where('transaction_id', $request->transaction_id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah mengulas produk pada transaksi ini.');
        }

        Review::create([
            'user_id'        => Auth::id(),
            'transaction_id' => $request->transaction_id,
            'product_id'     => $request->product_id,
            'rate'           => $request->rate,
            'comment'        => $request->comment,
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'rate'    => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $review = Review::findOrFail($id);

        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $review->update([
            'rate'    => $request->rate,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Ulasan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        if ($review->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}