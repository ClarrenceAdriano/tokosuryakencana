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
    public function index()
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('transactions.transaction', compact('transactions'));
    }

    public function create()
    {
        $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Keranjang kosong.');
        }

        $total = $cartItems->sum('subtotal');
        $addresses = Address::where('user_id', Auth::id())->get();

        return view('payment', compact('cartItems', 'total', 'addresses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'address_id'      => 'required|exists:addresses,id',
            'payment_method'  => 'required|string|in:transfer,cod,ewallet',
        ]);

        $cartItems = Cart::with('product')->where('user_id', Auth::id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('products.index');
        }

        try {
            DB::beginTransaction();

            $totalPay = $cartItems->sum('subtotal');

            $transaction = Transaction::create([
                'user_id'        => Auth::id(),
                'address_id'     => $request->address_id,
                'total'          => $totalPay,
                'payment_method' => $request->payment_method,
                'status'         => 'pending',
            ]);

            foreach ($cartItems as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);

                if (!$product || $product->stock < $item->quantity) {
                    DB::rollBack();
                    return back()->with('error', 'Stok produk ' . $item->product->name . ' tidak mencukupi.');
                }

                Transaction_detail::create([
                    'transaction_id' => $transaction->id,
                    'product_id'     => $item->product_id,
                    'quantity'       => $item->quantity,
                    'subtotal'       => $item->subtotal,
                ]);

                $product->decrement('stock', $item->quantity);
            }

            Cart::where('user_id', Auth::id())->delete();

            DB::commit();

            return redirect()->route('transaction.show', $transaction->id)
                ->with('success', 'Transaksi berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Transaction $transaction)
    {
        if ($transaction->user_id != Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $transaction->load(['transaction_details.product', 'user']);

        return view('transactions.detailTransaction', compact('transaction'));
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id != Auth::id() && Auth::user()->role != 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $nextStatus = match ($transaction->status) {
            'pending' => 'paid',
            'paid'    => 'shipped',
            'shipped' => 'done',
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