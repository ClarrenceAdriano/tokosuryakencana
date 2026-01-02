@extends('layouts.app')

@section('title', 'Home - Toko Surya Kencana')

@section('content')
    <div class="position-relative">

        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">

            <div class="carousel-indicators">
                @if ($carouselProducts->isNotEmpty())
                    @foreach ($carouselProducts as $key => $slider)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $key }}"
                            class="{{ $loop->first ? 'active' : '' }}" aria-current="true"></button>
                    @endforeach
                @endif
            </div>

            <div class="carousel-inner">
                @if ($carouselProducts->isNotEmpty())
                    @foreach ($carouselProducts as $slider)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <img src="{{ asset('storage/' . $slider->image) }}" class="d-block w-100"
                                style="height: 70vh; object-fit: cover; filter: brightness(0.6);" alt="{{ $slider->name }}">

                            <div class="carousel-caption d-none d-md-block pb-3">
                                <p class="fs-5">Rp {{ number_format($slider->price, 0, ',', '.') }}</p>
                                <a href="{{ route('products.detail', $slider->id) }}"
                                    class="btn btn-outline-light btn-sm mt-2">Lihat Detail</a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="carousel-item active">
                        <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?q=80&w=1920&auto=format&fit=crop"
                            class="d-block w-100" style="height: 70vh; object-fit: cover; filter: brightness(0.7);"
                            alt="Office">
                    </div>
                    <div class="carousel-item">
                        <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1920&auto=format&fit=crop"
                            class="d-block w-100" style="height: 70vh; object-fit: cover; filter: brightness(0.7);"
                            alt="Sport">
                    </div>
                @endif
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <div class="position-absolute top-50 start-50 translate-middle z-2 text-center w-75">
            <div class="bg-white bg-opacity-75 p-4 p-md-5 rounded-3 shadow-lg">
                <h1 class="display-5 fw-bold text-dark">Our Product</h1>
                <p class="fs-5 text-secondary d-none d-md-block mb-4">
                    Cari Barang yang anda butuhkan di toko kami.
                </p>
                <a class="btn btn-dark btn-lg px-5 py-2 fw-bold" href="{{ route('products.index') }}" role="button">
                    Belanja Sekarang
                </a>
            </div>
        </div>
    </div>

    <div class="container my-5 py-4">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="fw-bold">Rekomendasi Produk</h2>
                <div class="bg-warning mx-auto mt-2" style="width: 60px; height: 3px;"></div>
                <p class="text-muted mt-3">Pilihan terbaik untuk kebutuhan Anda hari ini</p>
            </div>
        </div>

        <div class="row g-4">

            @forelse($products as $product)
                <div class="col-6 col-md-3">
                    <div class="card h-100 border-0 shadow-sm position-relative">

                        <div
                            style="height: 250px; overflow: hidden; border-top-left-radius: var(--bs-card-inner-border-radius); border-top-right-radius: var(--bs-card-inner-border-radius);">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-100 h-100"
                                    style="object-fit: cover;" alt="{{ $product->name }}">
                            @else
                                <div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center">
                                    <span class="text-white small">No Image</span>
                                </div>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column">
                            @if ($product->category)
                                <span class="badge bg-info text-dark w-auto align-self-start mb-2">
                                    {{ $product->category->name }}
                                </span>
                            @endif

                            <h5 class="card-title fs-6 fw-bold text-truncate" title="{{ $product->name }}">
                                {{ $product->name }}
                            </h5>

                            <p class="card-text text-muted small mb-1">
                                Stok: {{ $product->stock }}
                            </p>

                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>

                                <a href="{{ route('products.detail', $product->id) }}" class="btn btn-sm btn-outline-dark">
                                    <i class="bi bi-arrow-right"></i> Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Belum ada produk yang tersedia.</p>
                </div>
            @endforelse

        </div>

        <div class="row mt-5">
            <div class="col-12 text-center">
                <a href="{{ route('products.index') }}" class="btn btn-outline-dark rounded-pill px-5 py-2">
                    Lihat Katalog Lengkap &rarr;
                </a>
            </div>
        </div>
    </div>
@endsection
