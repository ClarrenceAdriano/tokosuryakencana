@extends('layouts.app')
@section('title', 'Daftar Produk')
@section('content')

    <div class="container mt-5">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Daftar Produk</h2>

            @if (auth()->user()->role == 'admin')
                <div class="d-flex gap-2">
                    <a href="{{ route('brands.index') }}" class="btn btn-secondary">
                        Brand
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                        Kategori
                    </a>
                    <a href="{{ route('products.create') }}" class="btn btn-primary">
                        + Tambah Produk
                    </a>
                </div>
            @endif

        </div>

        @if ($products->isEmpty())
            <div class="card text-center">
                <div class="card-body">
                    <p class="text-muted mb-0">Belum ada produk tersedia.</p>
                </div>
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
                @foreach ($products as $product)
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                                    alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-secondary d-flex align-items-center justify-content-center"
                                    style="height: 200px;">
                                    <span class="text-white">No Image</span>
                                </div>
                            @endif

                            <div class="card-body">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text text-muted small mb-2">
                                    {{ $product->category->name ?? 'N/A' }} • {{ $product->brand->name ?? 'N/A' }}
                                </p>
                                <p class="text-primary fw-bold fs-5 mb-2">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>
                                <p class="text-muted small mb-3">Stok: {{ $product->stock }}</p>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('products.detail', $product->id) }}"
                                        class="btn btn-info btn-sm flex-fill">
                                        Detail
                                    </a>
                                    @if (auth()->user()->role == 'admin')
                                        <a href="{{ route('products.edit', $product->id) }}"
                                            class="btn btn-warning btn-sm flex-fill">
                                            Edit
                                        </a>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                            class="flex-fill" onsubmit="return confirm('Hapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>

        @endif

    </div>

@endsection
