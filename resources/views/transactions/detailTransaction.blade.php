@extends('layouts.app')

@section('title', 'Detail Transaksi #' . $transaction->id)

@section('content')
    <div class="container mt-5 mb-5">
        <div class="mb-4">
            <a href="{{ route('transaction.index') }}" class="text-decoration-none text-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow border-0">
                    <div class="card-header bg-white p-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="fw-bold mb-1">INVOICE</h4>
                                <span class="text-muted small">Order ID: #{{ $transaction->id }}</span>
                            </div>
                            <div class="text-end">
                                @php
                                    $badgeClass = match ($transaction->status) {
                                        'pending' => 'bg-warning text-dark',
                                        'paid', 'completed', 'done' => 'bg-success',
                                        'cancelled', 'failed' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} fs-6 px-3 py-2">
                                    {{ strtoupper($transaction->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        {{-- ... (Bagian Info Pengiriman & Produk tidak berubah) ... --}}
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <h6 class="text-uppercase text-muted small fw-bold mb-2">Info Pengiriman</h6>
                                @php
                                    $address = App\Models\Address::find($transaction->address_id);
                                @endphp

                                @if ($address)
                                    <p class="fw-bold mb-0">{{ $address->recipient_name ?? $transaction->user->name }}</p>
                                    <p class="mb-0 text-muted">{{ $address->address }}</p>
                                    <p class="mb-0 text-muted">
                                        {{ $address->city }}, {{ $address->postal_code }}
                                    </p>
                                    <p class="text-muted">Telp: {{ $address->phone_number }}</p>
                                @else
                                    <div class="alert alert-danger py-2">
                                        <small>Data alamat tidak ditemukan (Mungkin telah dihapus). <br> ID:
                                            {{ $transaction->address_id }}</small>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6 text-md-end">
                                <h6 class="text-uppercase text-muted small fw-bold mb-2">Detail Pembayaran</h6>
                                <p class="mb-1">
                                    <span class="text-muted">Tanggal Order:</span> <br>
                                    <strong>{{ $transaction->created_at->format('d F Y, H:i') }}</strong>
                                </p>
                                <p class="mb-0">
                                    <span class="text-muted">Metode Bayar:</span> <br>
                                    <strong class="text-uppercase">{{ $transaction->payment_method }}</strong>
                                </p>
                            </div>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-end">Harga Satuan</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaction->transaction_details as $detail)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if ($detail->product->image)
                                                        <img src="{{ asset('storage/' . $detail->product->image) }}"
                                                            alt="Img" class="rounded me-2"
                                                            style="width: 40px; height: 40px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <span class="fw-semibold">{{ $detail->product->name }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $detail->quantity }}</td>
                                            <td class="text-end">
                                                Rp {{ number_format($detail->product->price, 0, ',', '.') }}
                                            </td>
                                            <td class="text-end fw-bold">
                                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold fs-5">Total Bayar</td>
                                        <td class="text-end fw-bold fs-5 text-primary">
                                            Rp {{ number_format($transaction->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @php
                            $user = Auth::user();
                            $buttonLabel = null;
                            $buttonClass = '';

                            if ($user->role === 'admin') {
                                if ($transaction->status === 'pending') {
                                    $buttonLabel = 'Konfirmasi Pembayaran (Paid)';
                                    $buttonClass = 'btn-primary';
                                } elseif ($transaction->status === 'paid') {
                                    $buttonLabel = 'Kirim Barang (Shipped)';
                                    $buttonClass = 'btn-info text-white';
                                }
                            } 
                            else {
                                if ($transaction->status === 'shipped') {
                                    $buttonLabel = 'Selesaikan Pesanan (Diterima)';
                                    $buttonClass = 'btn-success';
                                }
                            }
                        @endphp

                        @if ($buttonLabel)
                            <div class="d-grid gap-2 mt-4">
                                <form action="{{ route('transaction.updateStatus', $transaction->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn {{ $buttonClass }} btn-lg w-100 shadow-sm">
                                        {{ $buttonLabel }} <i class="bi bi-arrow-right-circle ms-2"></i>
                                    </button>
                                </form>
                            </div>
                        @elseif($transaction->status == 'done')
                            <div class="alert alert-success text-center mt-4 mb-4">
                                <i class="bi bi-check-circle-fill"></i> Transaksi Selesai
                            </div>

                            <hr class="my-4">

                            <h5 class="fw-bold mb-3"><i class="bi bi-star"></i> Ulasan Produk Pesanan Ini</h5>
                            <div class="vstack gap-3">
                                @foreach ($transaction->transaction_details as $detail)
                                    @php
                                        $existingReview = \App\Models\Review::where('transaction_id', $transaction->id)
                                            ->where('product_id', $detail->product_id)
                                            ->first();
                                    @endphp
                                    <div class="card border bg-light">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start gap-3">
                                                @if ($detail->product->image)
                                                    <img src="{{ asset('storage/' . $detail->product->image) }}"
                                                        alt="Product" class="rounded"
                                                        style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center text-white"
                                                        style="width: 60px; height: 60px;">
                                                        <i class="bi bi-image"></i>
                                                    </div>
                                                @endif

                                                <div class="w-100">
                                                    <h6 class="fw-bold mb-2">{{ $detail->product->name }}</h6>
                                                    @if ($existingReview)
                                                        <div class="p-3 bg-white rounded border">
                                                            <div class="text-warning mb-1">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    <i class="bi bi-star{{ $i <= $existingReview->rate ? '-fill' : '' }}"></i>
                                                                @endfor
                                                                <span class="text-muted ms-2 small">({{ $existingReview->rate }}/5)</span>
                                                            </div>
                                                            <p class="mb-0 text-muted fst-italic">"{{ $existingReview->comment }}"</p>
                                                            <small class="text-success fw-bold mt-2 d-block">
                                                                <i class="bi bi-check-circle"></i> Ulasan terkirim
                                                            </small>
                                                        </div>
                                                    @else
                                                        <form action="{{ route('reviews.store') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                                                            <input type="hidden" name="product_id" value="{{ $detail->product_id }}">

                                                            <div class="row g-2">
                                                                <div class="col-md-3">
                                                                    <select name="rate" class="form-select form-select-sm" required>
                                                                        <option value="" selected disabled>Bintang</option>
                                                                        <option value="5">⭐⭐⭐⭐⭐</option>
                                                                        <option value="4">⭐⭐⭐⭐</option>
                                                                        <option value="3">⭐⭐⭐</option>
                                                                        <option value="2">⭐⭐</option>
                                                                        <option value="1">⭐</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <input type="text" name="comment" class="form-control form-control-sm" placeholder="Tulis ulasan..." required>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="submit" class="btn btn-primary btn-sm w-100">Kirim</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection