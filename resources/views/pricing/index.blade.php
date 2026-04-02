@extends('template.app')

@section('title', 'Pilih Paket Layanan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 text-center mb-5">
        <h2 class="fw-bold">Pilih Paket <span class="text-primary">Bengkel Digital</span> Anda</h2>
        <p class="text-muted">Kelola bengkel lebih efisien dengan fitur lengkap kami. <br> Mulai dengan gratis atau upgrade ke premium untuk akses tanpa batas.</p>
    </div>
</div>

<div class="row g-4 justify-content-center">
    <!-- Free Plan -->
    <div class="col-md-5">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header text-center bg-white pt-5">
                <h5 class="fw-bold text-uppercase text-muted">Trial Plan (Gratis)</h5>
                <h1 class="fw-black mb-0 display-4">Rp 0</h1>
            </div>
            <div class="card-body pt-0 text-center pb-5">
                <p class="text-muted small mb-4">Mencoba alur kerja aplikasi</p>
                <div class="dropdown-divider mb-4"></div>
                <ul class="list-unstyled text-start d-inline-block">
                    <li class="mb-3"><i class="bx bx-check-circle text-success me-2"></i> Akses Semua Fitur</li>
                    <li class="mb-3"><i class="bx bx-time text-primary me-2"></i> **Berlaku 3 Hari**</li>
                    <li class="mb-3 text-muted"><i class="bx bx-x-circle text-danger me-2"></i> Laporan Bulanan Terbatas</li>
                    <li class="mb-3 text-muted"><i class="bx bx-x-circle text-danger me-2"></i> Support Prioritas</li>
                </ul>
                <div class="mt-4">
                    <button class="btn btn-outline-primary px-5 disabled">Sedang Digunakan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Plan -->
    <div class="col-md-5">
        <div class="card h-100 shadow border-primary border-2">
            <div class="card-header text-center bg-primary pt-5 text-white position-relative overflow-hidden">
                 <div class="ribbon" style="position: absolute; top: 15px; right: -30px; transform: rotate(45deg); background: #ffc107; padding: 5px 40px; font-size: 10px; font-weight: bold; color: #000; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">REKOMENDASI</div>
                <h5 class="fw-bold text-uppercase text-white-50">Sultan Pack (Premium)</h5>
                <h1 class="fw-black mb-0 display-4 text-white">Rp 25.000</h1>
            </div>
            <div class="card-body pt-4 text-center pb-5">
                <p class="text-muted small mb-4">Investasi cerdas untuk bengkel maju</p>
                <div class="dropdown-divider mb-4"></div>
                <ul class="list-unstyled text-start d-inline-block">
                    <li class="mb-3"><i class="bx bx-check-double text-success me-2"></i> **Akses Semua Fitur Selamanya**</li>
                    <li class="mb-3"><i class="bx bx-calendar-star text-success me-2"></i> Berlaku 1 Bulan Penanganan</li>
                    <li class="mb-3"><i class="bx bx-cloud-upload text-success me-2"></i> Cloud Storage Aman</li>
                    <li class="mb-3"><i class="bx bx-support text-success me-2"></i> Support Fast Respon</li>
                    <li class="mb-3"><i class="bx bx-printer text-success me-2"></i> Cetak Invoice Tanpa Batas</li>
                </ul>
                <div class="mt-4">
                    <a href="https://wa.me/{{ Auth::user()->phone ?? '62812345678' }}?text=Halo,%20saya%20ingin%20upgrade%20ke%20Sultan%20Pack%20Bengkel%20Digital!" class="btn btn-primary px-5 btn-lg shadow">
                         <i class="bx bx-rocket me-1"></i> Upgrade Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-12 text-center text-muted small">
        <p>Butuh bantuan? <a href="#" class="text-primary fw-bold">Hubungi Tim Support</a></p>
    </div>
</div>

<style>
    .fw-black { font-weight: 900 !important; }
</style>
@endsection
