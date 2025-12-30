<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class AdminTransactionController extends Controller
{
    /**
     * 1. INDEX: Dashboard Transaksi Admin
     * Method ini bertugas mengambil SEMUA data transaksi,
     * lalu mengelompokkannya berdasarkan status untuk ditampilkan di dashboard.
     */
    public function index()
    {
        // A. Fetch Data Utama (Query Database)
        // Mengambil seluruh data transaksi dari database sekaligus.
        // - with('user'): Eager loading data user agar query efisien (mencegah N+1 problem).
        // - latest(): Mengurutkan dari yang paling baru dibuat.
        $transactions = Transaction::with('user')->latest()->get();
        
        // 1. Filter status 'pending' (Menunggu Pembayaran)
        $pending = $transactions->where('status', 'pending');
        
        // 2. Filter status 'paid' (Sudah Dibayar - Perlu dikonfirmasi/dikirim)
        $paid    = $transactions->where('status', 'paid');
        
        // 3. Filter status 'shipped' (Sedang dalam pengiriman)
        $shipped = $transactions->where('status', 'shipped');
        
        // 4. Filter status 'done' (Transaksi Selesai)
        $done    = $transactions->where('status', 'done');

        // C. Return View
        // Mengirimkan ke-4 variabel yang sudah dipilah ke view 'admin.dashboard'
        return view('admin.dashboard', compact('pending', 'paid', 'shipped', 'done'));
    }
}