@extends('layouts.app')

@section('title', 'Detail Produk: ' . $product->name)

@section('content')
    <div class="container mt-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-5 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-1">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded w-100"
                                alt="{{ $product->name }}" style="object-fit: cover; max-height: 500px;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                style="height: 400px;">
                                <span class="text-muted">Tidak ada gambar</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">

                        <div class="mb-2">
                            <span class="badge bg-primary me-2">
                                <i class="bi bi-tag"></i> {{ $product->category->name ?? 'Tanpa Kategori' }}
                            </span>
                            <span class="badge bg-secondary">
                                <i class="bi bi-shop"></i> {{ $product->brand->name ?? 'Tanpa Brand' }}
                            </span>
                        </div>

                        <h1 class="fw-bold mb-3">{{ $product->name }}</h1>

                        <h2 class="text-primary fw-bold mb-3">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </h2>

                        <div class="mb-3">
                            @if ($product->stock > 0)
                                <span class="text-success fw-bold">
                                    <i class="bi bi-check-circle-fill"></i> Stok Tersedia: {{ $product->stock }}
                                </span>
                            @else
                                <span class="text-danger fw-bold">
                                    <i class="bi bi-x-circle-fill"></i> Stok Habis
                                </span>
                            @endif
                        </div>

                        @auth
                            @if ($product->stock > 0)
                                <div class="card bg-light border-0 p-3 mb-4">
                                    <form action="{{ route('cart.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold text-muted">Jumlah</label>
                                                <input type="number" name="quantity" class="form-control" value="1"
                                                    min="1" max="{{ $product->stock }}">
                                            </div>
                                            <div class="col-md-8">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="bi bi-cart-plus-fill"></i> Masukkan Keranjang
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="alert alert-warning mb-4">
                                    Produk ini sedang tidak tersedia.
                                </div>
                            @endif
                        @else
                            <div class="alert alert-info mb-4">
                                Silakan <a href="{{ route('login') }}" class="alert-link">Login</a> untuk membeli produk ini.
                            </div>
                        @endauth

                        <hr>

                        <div class="mb-4">
                            <h5 class="fw-bold">Deskripsi Produk</h5>
                            <p class="text-muted" style="white-space: pre-line;">
                                {{ $product->description }}
                            </p>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            @if (auth()->user()->role == 'admin')
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning px-4">
                                    Edit Produk
                                </a>

                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('products.index') }}" class="btn btn-secondary ms-auto">
                                Kembali
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5 mb-5">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Ulasan Pembeli</h4>

                        {{-- 1. Ringkasan Rating --}}
                        @php
                            $totalReviews = $product->reviews->count();
                            $avgRating = $product->reviews->avg('rate');
                        @endphp

                        <div class="d-flex align-items-center mb-4 p-3 bg-light rounded">
                            <div class="text-center me-4">
                                <h1 class="fw-bold display-4 mb-0 text-warning">{{ number_format($avgRating, 1) }}</h1>
                                <div class="text-warning small">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= round($avgRating) ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                                <small class="text-muted">{{ $totalReviews }} Ulasan</small>
                            </div>
                            <div class="border-start ps-4">
                                <p class="mb-0 text-muted">
                                    Cek apa kata pembeli tentang produk <strong>{{ $product->name }}</strong> ini.
                                </p>
                            </div>
                        </div>

                        <hr>

                        {{-- 2. Daftar Review --}}
                        @forelse($product->reviews as $review)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        {{-- Avatar Placeholder --}}
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px; font-weight: bold;">
                                            {{ substr($review->user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0">{{ $review->user->name ?? 'Pengguna' }}</h6>
                                            <div class="text-warning small">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="bi bi-star{{ $i <= $review->rate ? '-fill' : '' }}"></i>
                                                @endfor
                                                <span class="text-muted ms-1" style="font-size: 0.8rem">
                                                    ({{ $review->rate }}/5)
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                </div>

                                <div class="ps-5">
                                    <p class="text-muted mb-0 bg-light p-3 rounded" style="font-style: italic;">
                                        "{{ $review->comment }}"
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-chat-square-dots fs-1 text-muted mb-3"></i>
                                <p class="text-muted">Belum ada ulasan untuk produk ini.</p>
                                @auth
                                    <p class="small">Jadilah yang pertama membeli dan memberikan ulasan!</p>
                                @endauth
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>
    @endsection
