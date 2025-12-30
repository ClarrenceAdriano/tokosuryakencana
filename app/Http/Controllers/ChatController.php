<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * 1. INDEX: Entry Point untuk Customer
     * Logika: Customer tidak memilih lawan bicara. Sistem otomatis menghubungkan
     * customer dengan 'Admin' pertama yang ditemukan di database.
     */
    public function index()
    {
        // Cari user yang role-nya admin
        $admin = User::where('role', 'admin')->first();

        // Safety Check: Jika tidak ada admin di database
        if (!$admin) return back()->with('error', 'Admin tidak ditemukan.');

        // Redirection Logic:
        // Jika yang login ternyata Admin sendiri, jangan buka halaman chat customer.
        // Lempar dia ke Dashboard Inbox Admin.
        if (Auth::id() == $admin->id) return redirect()->route('chat.admin'); 

        // Tampilkan halaman chat customer, set lawan bicaranya adalah Admin
        return view('chat.chat', ['receiver' => $admin]);
    }

    /**
     * 2. DASHBOARD: Inbox Admin
     * Menampilkan daftar semua percakapan yang masuk ke Admin.
     */
    public function dashboard()
    {
        // Query Kompleks untuk mengambil daftar chat:
        $chats = Chat::whereHas('chat_users', function ($q) {
            // 1. Ambil Chat dimana User yang login (Admin) terdaftar sebagai peserta
            $q->where('user_id', Auth::id());
        })
            ->with(['messages' => function ($q) {
                // 2. Eager Load pesan TERAKHIR saja (untuk preview di list: "Halo...")
                $q->latest(); 
            }, 'chat_users.user']) // 3. Eager Load data user lawan bicara (Nama, Avatar)
            ->latest() // Urutkan chat berdasarkan aktivitas terbaru
            ->get();

        return view('chat.admin', compact('chats'));
    }

    /**
     * 3. CHAT USER: Membuka Room Chat Spesifik
     * Digunakan saat Admin mengklik nama user di Inbox, atau untuk direct link.
     */
    public function chatUser($id)
    {
        $receiver = \App\Models\User::findOrFail($id);

        // Cari Room Chat yang Valid (Private Chat)
        // Logika: Cari chat yang memiliki user_id SAYA (Auth) DAN user_id LAWAN ($id)
        $chat = \App\Models\Chat::whereHas('chat_users', function ($q) use ($id) {
            $q->where('user_id', $id);
        })->whereHas('chat_users', function ($q) {
            $q->where('user_id', Auth::id());
        })->first();

        // --- LOGIKA "MARK AS READ" (Hapus Dot Biru) ---
        // Jika chat ditemukan DAN statusnya masih 'unread' (ada pesan baru dari customer),
        // maka saat Admin membuka method ini, status diubah jadi 'active' (terbaca).
        if ($chat && $chat->status == 'unread') {
            $chat->update(['status' => 'active']);
        }
        // ----------------------------------------------

        return view('chat.index', ['receiver' => $receiver]);
    }
}