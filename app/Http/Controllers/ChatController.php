<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) return back()->with('error', 'Admin tidak ditemukan.');

        if (Auth::id() == $admin->id) return redirect()->route('chat.admin'); 

        return view('chat.chat', ['receiver' => $admin]);
    }

    public function dashboard()
    {
        $chats = Chat::whereHas('chat_users', function ($q) {
            $q->where('user_id', Auth::id());
        })
            ->with(['messages' => function ($q) {
                $q->latest(); 
            }, 'chat_users.user']) 
            ->latest() 
            ->get();

        return view('chat.admin', compact('chats'));
    }

    public function chatUser($id)
    {
        $receiver = \App\Models\User::findOrFail($id);

        $chat = \App\Models\Chat::whereHas('chat_users', function ($q) use ($id) {
            $q->where('user_id', $id);
        })->whereHas('chat_users', function ($q) {
            $q->where('user_id', Auth::id());
        })->first();

        if ($chat && $chat->status == 'unread') {
            $chat->update(['status' => 'active']);
        }

        return view('chat.index', ['receiver' => $receiver]);
    }
}