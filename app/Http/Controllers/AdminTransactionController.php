<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class AdminTransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('user')->latest()->get();
        
        $pending = $transactions->where('status', 'pending');
        
        $paid    = $transactions->where('status', 'paid');
        
        $shipped = $transactions->where('status', 'shipped');
        
        $done    = $transactions->where('status', 'done');

        return view('admin.dashboard', compact('pending', 'paid', 'shipped', 'done'));
    }
}