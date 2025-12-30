<?php

namespace App\Livewire;

use App\Models\Chat;
use App\Models\Chat_user;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatComponent extends Component
{
    // Variable public ini bisa diakses langsung dari View (Blade)
    public $chat;          // Object Chat Room yang aktif saat ini
    public $receiver;      // Object User lawan bicara (Admin/Customer)
    public $messageText;   // Data Binding untuk input text box (wire:model="messageText")

    /**
     * 1. MOUNT: Persiapan Awal
     */
    public function mount($receiver_id)
    {
        $this->receiver = User::findOrFail($receiver_id);
        $myId = Auth::id();

        // A. LOGIKA CARI ROOM (Find Existing Room)
        // Kita mencari Room dimana "Saya ada di sana" DAN "Dia ada di sana".
        // Menggunakan whereHas berantai untuk memfilter relasi Many-to-Many.
        $existingChat = Chat::whereHas('chat_users', function ($q) use ($myId) {
            $q->where('user_id', $myId);
        })->whereHas('chat_users', function ($q) use ($receiver_id) {
            $q->where('user_id', $receiver_id);
        })->first();

        // B. LOGIKA BUAT ROOM BARU (Create New Room)
        // Jika tidak ditemukan room (artinya ini chat pertama kali), kita buat baru.
        if (!$existingChat) {
            
            // 1. Buat Header Room di tabel 'chats'
            $existingChat = Chat::create([
                'title'  => 'Private Chat ' . $myId . '-' . $receiver_id,
                'status' => 'active'
            ]);

            // 2. Daftarkan SAYA sebagai peserta
            Chat_user::create([
                'chat_id' => $existingChat->id,
                'user_id' => $myId
            ]);

            // 3. Daftarkan DIA (Lawan Bicara) sebagai peserta
            Chat_user::create([
                'chat_id' => $existingChat->id,
                'user_id' => $receiver_id
            ]);
        }

        // Simpan room yang ditemukan/dibuat ke property agar bisa dipakai method lain
        $this->chat = $existingChat;
    }

    /**
     * 2. SEND MESSAGE: Mengirim Pesan
     * Dipanggil saat user menekan tombol kirim / enter.
     */
    public function sendMessage()
    {
        // Validasi: Pesan tidak boleh kosong & maksimal 2000 karakter
        $this->validate([
            'messageText' => 'required|string|max:2000',
        ]);

        // Simpan pesan ke database
        Message::create([
            'chat_id' => $this->chat->id,
            'user_id' => Auth::id(),
            'message' => $this->messageText,
        ]);

        // --- LOGIKA NOTIFIKASI ADMIN (PENTING) ---
        // Jika pengirimnya ADALAH Customer (Bukan Admin),
        // Kita ubah status chat jadi 'unread'.
        // Ini akan memicu "Dot Biru" atau notifikasi di dashboard Admin.
        if (Auth::user()->role !== 'admin') {
            $this->chat->update(['status' => 'unread']);
        }

        // Bersihkan input box setelah kirim
        $this->reset('messageText');

        // Kirim sinyal ke JavaScript (opsional, misal untuk scroll ke bawah)
        $this->dispatch('message-sent');
    }

    /**
     * 3. RENDER: Tampilan Visual
     * Method ini dipanggil otomatis oleh Livewire setiap kali ada update data (polling).
     */
    public function render()
    {
        // Ambil pesan-pesan dari database
        $messages = $this->chat->messages()
            ->with('user') // Eager Loading: Ambil data nama/foto pengirim sekalian
            ->oldest()     // Urutkan dari Terlama ke Terbaru (Chat flow: Atas -> Bawah)
            ->get();

        return view('livewire.chat-component', [
            'messages' => $messages,
            'receiver' => $this->receiver
        ]);
    }
}