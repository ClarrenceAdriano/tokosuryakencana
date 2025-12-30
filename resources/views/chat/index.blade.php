@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <a href="{{ auth()->user()->role == 'admin' ? route('chat.admin') : url('/') }}" class="text-decoration-none text-muted mb-3 d-block">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>

            @livewire('chat-component', ['receiver_id' => $receiver->id])
            
        </div>
    </div>
</div>
@endsection