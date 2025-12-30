@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">Riwayat Transaksi</h2>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                Belanja Lagi
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Total Belanja</th>
                                <th>Status</th>
                                <th>Metode Bayar</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                    <td class="fw-bold text-primary">
                                        Rp {{ number_format($transaction->total, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match ($transaction->status) {
                                                'pending' => 'bg-warning text-dark',
                                                'paid', 'completed' => 'bg-success',
                                                'cancelled', 'failed' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} rounded-pill px-3">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                    <td class="text-uppercase small text-muted">
                                        {{ $transaction->payment_method }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('transaction.show', $transaction->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted mb-2">
                                            <i class="bi bi-receipt display-4"></i>
                                        </div>
                                        <h5 class="text-muted">Belum ada riwayat transaksi.</h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
