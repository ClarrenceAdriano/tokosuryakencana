@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Daftar Pesan Masuk</h3>

    <div class="card shadow-sm">
        <div class="list-group list-group-flush">
            @forelse($chats as $chat)
                @php
                    $opponent = $chat->chat_users->where('user_id', '!=', auth()->id())->first()->user ?? null;
                    $lastMessage = $chat->messages->first();
                    $isUnread = $chat->status == 'unread';
                @endphp

                @if($opponent)
                    <a href="{{ route('chat.with', $opponent->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="mb-0 fw-bold {{ $isUnread ? 'text-dark' : 'text-secondary' }}">
                                        {{ $opponent->username }}
                                    </h6>
                                    @if($isUnread)
                                        <span class="badge bg-primary" style="font-size: 0.6rem;">Baru</span>
                                    @endif
                                </div>
                                
                                <small class="{{ $isUnread ? 'text-dark fw-bold' : 'text-muted' }} text-truncate d-inline-block" style="max-width: 250px;">
                                    {{ $lastMessage ? $lastMessage->message : 'Belum ada pesan' }}
                                </small>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <small class="text-muted d-block">{{ $chat->updated_at->diffForHumans() }}</small>
                            <span class="badge {{ $isUnread ? 'bg-primary' : 'bg-secondary' }} rounded-pill">
                                {{ $isUnread ? 'Balas' : 'Buka' }}
                            </span>
                        </div>
                    </a>
                @endif
            @empty
                <div class="p-5 text-center text-muted">
                    Belum ada percakapan.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection