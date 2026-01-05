@extends('layouts.app')

@section('title', 'Checkout & Pembayaran')

@section('content')
    <div class="container mt-5 mb-5">

        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('cart.index') }}" class="text-decoration-none text-secondary me-3">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h2 class="fw-bold mb-0">Pengiriman & Pembayaran</h2>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('transaction.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Alamat Pengiriman
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($addresses->isEmpty())
                                <div class="text-center py-4">
                                    <p class="text-muted">Anda belum memiliki alamat tersimpan.</p>
                                    <a href="{{ route('addresses.create') }}" class="btn btn-outline-primary">
                                        + Tambah Alamat Baru
                                    </a>
                                </div>
                            @else
                                <div class="list-group">
                                    @foreach ($addresses as $address)
                                        <label class="list-group-item d-flex gap-3 align-items-start cursor-pointer">
                                            <input class="form-check-input flex-shrink-0" type="radio" name="address_id"
                                                id="addr-{{ $address->id }}" value="{{ $address->id }}"
                                                {{ $loop->first ? 'checked' : '' }}>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-0 fw-bold">
                                                        {{ $address->name ?? Auth::user()->name }}</h6>
                                                    @if ($address->is_default)
                                                        <span class="badge bg-secondary">Utama</span>
                                                    @endif
                                                </div>
                                                <p class="mb-0 text-muted small mt-1">
                                                    {{ $address->recipient_name }}, {{ $address->address }}, {{ $address->city }}
                                                    {{ $address->postal_code }}
                                                </p>
                                                <small class="text-muted">Telp: {{ $address->phone_number }}</small>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('addresses.create') }}" class="text-decoration-none small fw-bold">
                                        + Tambah Alamat Lain
                                    </a>
                                </div>
                            @endif
                            @error('address_id')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-credit-card-2-front-fill text-primary me-2"></i>Metode
                                Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="payment_method" id="pay-transfer"
                                        value="transfer" checked>
                                    <label class="btn btn-outline-secondary w-100 h-100 py-3 text-start" for="pay-transfer">
                                        <i class="bi bi-bank fs-4 mb-2 d-block"></i>
                                        <span class="fw-bold">Transfer Bank <br> BCA 4054383888 <br> A. N Albert Tengkawan </span>
                                    </label>
                                </div>
                            </div>
                            @error('payment_method')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 position-sticky" style="top: 2rem;">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">Ringkasan Pesanan</h5>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @foreach ($cartItems as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="badge bg-light text-dark border me-2">{{ $item->quantity }}x</span>
                                            <div class="text-truncate" style="max-width: 150px;">
                                                {{ $item->product->name }}
                                            </div>
                                        </div>
                                        <span class="text-muted small">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-footer bg-white p-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal Produk</span>
                                <span class="fw-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-muted">Ongkos Kirim</span>
                                <span class="text-success fw-bold">Gratis</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="h5 fw-bold">Total Tagihan</span>
                                <span class="h4 fw-bold text-primary">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-5"
                                {{ $addresses->isEmpty() ? 'disabled' : '' }}>
                                <i class="bi bi-lock-fill me-2"></i> Bayar Sekarang
                            </button>

                            @if ($addresses->isEmpty())
                                <small class="text-danger d-block text-center mt-2">
                                    *Harap tambah alamat pengiriman terlebih dahulu.
                                </small>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
@endsection
