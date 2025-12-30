@extends('layouts.app')

@section('title', 'Chat dengan Admin')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="mb-3">
                <h3>Halaman Bantuan</h3>
                <p class="text-muted">Silakan hubungi admin di bawah ini.</p>
            </div>
            @livewire('chat-component', ['receiver_id' => $receiver->id])

        </div>
    </div>
</div>
@endsection