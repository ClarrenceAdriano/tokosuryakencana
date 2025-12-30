@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">Dashboard Transaksi</h2>
        <span class="badge bg-dark">Admin Area</span>
    </div>

    @php
        $tabs = [
            ['id' => 'pending', 'label' => 'Pending', 'color' => 'warning', 'items' => $pending],
            ['id' => 'paid',    'label' => 'Paid',    'color' => 'info text-white', 'items' => $paid],
            ['id' => 'shipped', 'label' => 'Shipped', 'color' => 'primary', 'items' => $shipped],
            ['id' => 'done',    'label' => 'Done',    'color' => 'success', 'items' => $done],
        ];
    @endphp

    <ul class="nav nav-pills mb-4 gap-2" id="trxTabs" role="tablist">
        @foreach($tabs as $tab)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $loop->first ? 'active' : '' }} fw-bold d-flex align-items-center gap-2" 
                        id="{{ $tab['id'] }}-tab" 
                        data-bs-toggle="pill" 
                        data-bs-target="#{{ $tab['id'] }}" 
                        type="button" 
                        role="tab">
                    <span class="badge bg-{{ str_replace(' text-white', '', $tab['color']) }} rounded-circle p-1" style="width: 10px; height: 10px;"></span>
                    {{ $tab['label'] }}
                    <span class="badge bg-secondary ms-1">{{ $tab['items']->count() }}</span>
                </button>
            </li>
        @endforeach
    </ul>
    <div class="tab-content shadow-sm rounded bg-white border" id="trxTabsContent">
        
        @foreach($tabs as $tab)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tab['id'] }}" role="tabpanel">
                
                <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-uppercase text-secondary">
                        List Transaksi: <span class="text-{{ str_replace(' text-white', '', $tab['color']) }}">{{ $tab['label'] }}</span>
                    </h6>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Order ID</th>
                                <th class="py-3">Customer</th>
                                <th class="py-3">Tanggal</th>
                                <th class="py-3 text-end">Total Bayar</th>
                                <th class="py-3 text-center">Metode</th>
                                <th class="py-3 text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tab['items'] as $trx)
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">#{{ $trx->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                                {{ substr($trx->user->name ?? 'G', 0, 1) }}
                                            </div>
                                            <span>{{ $trx->user->name ?? 'Guest' }}</span>
                                        </div>
                                    </td>
                                    <td class="small text-muted">
                                        {{ $trx->created_at->format('d M Y') }} <br>
                                        {{ $trx->created_at->format('H:i') }}
                                    </td>
                                    <td class="text-end fw-bold">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge border border-secondary text-secondary fw-normal">
                                            {{ strtoupper($trx->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('transaction.show', $trx->id) }}" class="btn btn-sm btn-primary">
                                            Detail <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" alt="Empty" style="width: 60px; opacity: 0.5;">
                                        <p class="text-muted mt-2 mb-0">Belum ada transaksi dengan status <strong>{{ $tab['label'] }}</strong></p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="p-3 bg-light border-top text-end small text-muted">
                    Total {{ $tab['items']->count() }} Data ditampilkan
                </div>
            </div>
        @endforeach

    </div>
</div>
@endsection