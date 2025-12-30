@extends('layouts.app')

@section('title', 'Daftar Alamat')

@section('content')

    <div class="container mt-5">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Address</h2>
            <a href="{{ route('addresses.create') }}" class="btn btn-primary">
                + Tambah Alamat
            </a>
        </div>

        @if ($addresses->isEmpty())
            <div class="card text-center">
                <div class="card-body">
                    <p class="text-muted mb-0">Belum ada alamat tersimpan.</p>
                </div>
            </div>
        @else
            @foreach ($addresses as $address)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1">{{ $address->name }}</h5>
                                @if ($address->is_default)
                                    <span class="badge bg-success">Alamat Utama</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1"><strong>Penerima:</strong> {{ $address->recipient_name }}</p>
                            <p class="mb-1"><strong>No. Telp:</strong> {{ $address->phone_number }}</p>
                            <p class="mb-1"><strong>Alamat:</strong> {{ $address->address }}</p>
                            <p class="mb-1"><strong>Kota:</strong> {{ $address->city }}</p>
                            <p class="mb-0"><strong>Kode Pos:</strong> {{ $address->postal_code }}</p>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('addresses.edit', $address->id) }}" class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            <form action="{{ route('addresses.destroy', $address->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Hapus alamat ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    Hapus
                                </button>
                            </form>

                            @unless ($address->is_default)
                                <form action="{{ route('addresses.setDefault', $address->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        Jadikan Utama
                                    </button>
                                </form>
                            @endunless
                        </div>

                    </div>
                </div>
            @endforeach

        @endif

    </div>

@endsection
