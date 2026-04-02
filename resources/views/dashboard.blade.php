@extends('template.app')
@section('title', 'Dashboard')
@section('content')
@if(!$isChecklistComplete)
<div class="row">
    <div class="col-lg-12 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Selamat datang, {{ Auth::user()->name }}! 🎉</h5>
                        <p class="mb-4">
                            Wah, bengkel <span class="fw-bold">{{ Auth::user()->nama_bengkel }}</span> sudah terdaftar! Selesaikan checklist di bawah ini untuk mulai mengelola bengkel Anda secara digital.
                        </p>

                        <div class="checklist">
                            <h6 class="fw-bold mb-3">📋 Checklist Persiapan:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-3 d-flex align-items-center">
                                    @if($layananCount > 0)
                                        <i class="bx bxs-check-circle text-success fs-4 me-2"></i>
                                    @else
                                        <i class="bx bx-circle text-secondary fs-4 me-2"></i>
                                    @endif
                                    <span class="fs-6 {{ $layananCount > 0 ? 'text-decoration-line-through text-muted' : '' }}">Tambah layanan (Service, Oli, Ban, dll)</span>
                                </li>
                                <li class="mb-3 d-flex align-items-center">
                                    @if($sparepartCount > 0)
                                        <i class="bx bxs-check-circle text-success fs-4 me-2"></i>
                                    @else
                                        <i class="bx bx-circle text-secondary fs-4 me-2"></i>
                                    @endif
                                    <span class="fs-6 {{ $sparepartCount > 0 ? 'text-decoration-line-through text-muted' : '' }}">Tambah stok sparepart</span>
                                </li>
                                <li class="mb-3 d-flex align-items-center">
                                    @if($transaksiCount > 0)
                                        <i class="bx bxs-check-circle text-success fs-4 me-2"></i>
                                    @else
                                        <i class="bx bx-circle text-secondary fs-4 me-2"></i>
                                    @endif
                                    <span class="fs-6 {{ $transaksiCount > 0 ? 'text-decoration-line-through text-muted' : '' }}">Buat transaksi pertama Anda</span>
                                </li>
                            </ul>
                        </div>

                        <div class="alert alert-info d-flex align-items-center p-3 mt-4" role="alert">
                            <span class="badge badge-center rounded-pill bg-info me-3"><i class="bx bx-bulb fs-4"></i></span>
                            <div>
                                💡 <strong>Tips:</strong> Ini akan membantu Anda memahami alur kerja aplikasi secara langsung dan cepat!
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img
                            src="{{ asset('template/assets/img/illustrations/man-with-laptop-light.png') }}"
                            height="140"
                            alt="View Badge User"
                            data-app-dark-img="illustrations/man-with-laptop-dark.png"
                            data-app-light-img="illustrations/man-with-laptop-light.png"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <!-- Total Pendapatan -->
    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-success"><i class="bx bx-wallet fs-4"></i></span>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Total Pendapatan</span>
                <h4 class="card-title mb-2">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> Semua Waktu</small>
            </div>
        </div>
    </div>

    <!-- Pendapatan Hari Ini -->
    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-calendar-star fs-4"></i></span>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Hari Ini</span>
                <h4 class="card-title mb-2 text-primary">Rp {{ number_format($todayIncome, 0, ',', '.') }}</h4>
                <small class="text-muted fw-semibold">Pemasukan Hari Ini</small>
            </div>
        </div>
    </div>

    <!-- Total Pelanggan -->
    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-info"><i class="bx bx-user-circle fs-4"></i></span>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Total Pelanggan</span>
                <h4 class="card-title mb-2">{{ $customerCount }}</h4>
                <small class="text-muted fw-semibold">Orang Terdaftar</small>
            </div>
        </div>
    </div>

    <!-- Stok Menipis -->
    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-{{ $lowStockCount > 0 ? 'danger' : 'secondary' }}">
                            <i class="bx bx-package fs-4"></i>
                        </span>
                    </div>
                </div>
                <span class="fw-semibold d-block mb-1">Stok Menipis</span>
                <h4 class="card-title mb-2 {{ $lowStockCount > 0 ? 'text-danger' : '' }}">{{ $lowStockCount }}</h4>
                <small class="text-{{ $lowStockCount > 0 ? 'danger' : 'muted' }} fw-semibold">
                    {{ $lowStockCount > 0 ? 'Segera isi stok!' : 'Stok aman' }}
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
@endpush
