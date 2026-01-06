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
    public $chat;
    public $receiver;
    public $messageText;

    public function mount($receiver_id)
    {
        $this->receiver = User::findOrFail($receiver_id);
        $myId = Auth::id();

        $existingChat = Chat::whereHas('chat_users', function ($q) use ($myId) {
            $q->where('user_id', $myId);
        })->whereHas('chat_users', function ($q) use ($receiver_id) {
            $q->where('user_id', $receiver_id);
        })->first();

        if (!$existingChat) {
            
            $existingChat = Chat::create([
                'title'  => 'Private Chat ' . $myId . '-' . $receiver_id,
                'status' => 'active'
            ]);

            Chat_user::create([
                'chat_id' => $existingChat->id,
                'user_id' => $myId
            ]);

            Chat_user::create([
                'chat_id' => $existingChat->id,
                'user_id' => $receiver_id
            ]);
        }

        $this->chat = $existingChat;
    }

    public function sendMessage()
    {
        $this->validate([
            'messageText' => 'required|string|max:2000',
        ]);

        Message::create([
            'chat_id' => $this->chat->id,
            'user_id' => Auth::id(),
            'message' => $this->messageText,
        ]);

        if (Auth::user()->role !== 'admin') {
            $this->chat->update(['status' => 'unread']);
        }

        $this->reset('messageText');

        $this->dispatch('message-sent');
    }

    public function render()
    {
        $messages = $this->chat->messages()
            ->with('user')
            ->oldest()
            ->get();

        return view('livewire.chat-component', [
            'messages' => $messages,
            'receiver' => $this->receiver
        ]);
    }
}