@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">Keranjang Belanja</h2>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Lanjut Belanja
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($cartItems->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="bi bi-cart-x display-1 text-muted"></i>
                </div>
                <h4 class="text-muted">Keranjang Anda masih kosong.</h4>
                <p class="mb-4">Yuk, cari produk impianmu sekarang!</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary px-4 py-2">Mulai Belanja</a>
            </div>
        @else
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40%;">Produk</th>
                                            <th style="width: 20%;">Harga</th>
                                            <th style="width: 20%;">Jumlah</th>
                                            <th style="width: 10%;">Subtotal</th>
                                            <th style="width: 10%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cartItems as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if ($item->product->image)
                                                            <img src="{{ asset('storage/' . $item->product->image) }}"
                                                                class="rounded me-3"
                                                                style="width: 60px; height: 60px; object-fit: cover;"
                                                                alt="{{ $item->product->name }}">
                                                        @else
                                                            <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center"
                                                                style="width: 60px; height: 60px;">
                                                                <span class="text-white small">No img</span>
                                                            </div>
                                                        @endif

                                                        <div>
                                                            <h6 class="mb-0 fw-semibold">{{ $item->product->name }}</h6>
                                                            <small class="text-muted">
                                                                Stok: {{ $item->product->stock }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                                </td>
                                                <td>
                                                    <form action="{{ route('cart.update', $item->id) }}" method="POST"
                                                        class="d-flex gap-1">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="number" name="quantity" value="{{ $item->quantity }}"
                                                            min="1" max="{{ $item->product->stock }}"
                                                            class="form-control form-control-sm text-center"
                                                            style="width: 60px;">
                                                        <button type="submit" class="btn btn-sm btn-light border" title="Update Qty">
                                                            Update
                                                        </button>
                                                    </form>
                                                </td>
                                                <td class="fw-bold text-primary">
                                                    Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                                                </td>
                                                <td>
                                                    <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-outline-danger border-0"
                                                            onclick="return confirm('Hapus produk ini dari keranjang?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">Ringkasan Belanja</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total Item</span>
                                <span class="fw-semibold">{{ $cartItems->sum('quantity') }} Pcs</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="h5 fw-bold">Total Harga</span>
                                <span class="h5 fw-bold text-primary">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('transaction.create') }}" class="btn btn-primary py-2 fw-bold">
                                    Checkout Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection