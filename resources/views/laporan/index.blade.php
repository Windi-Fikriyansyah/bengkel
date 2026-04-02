@extends('template.app')
@section('title', 'Laporan Transaksi')
@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white pb-0">
                    <h5 class="fw-bold"><i class="bx bx-filter-alt me-2 text-primary"></i> Filter Laporan</h5>
                </div>
                <div class="card-body pt-3">
                    <form action="{{ route('laporan.index') }}" method="GET" class="row align-items-end g-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">DARI TANGGAL</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">SAMPAI TANGGAL</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bx bx-search me-1"></i> Tampilkan
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                                <i class="bx bx-printer me-1"></i> Cetak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="row g-4 mb-4 mt-2">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-label-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <span class="badge bg-white text-primary rounded-pill p-2"><i class="bx bx-receipt fs-5"></i></span>
                    </div>
                    <p class="text-muted small mb-1 fw-bold">TOTAL TRANSAKSI</p>
                    <h3 class="fw-bold mb-0 text-primary">{{ $summary['total_transaksi'] }}</h3>
                    <small class="text-muted">Item Nota Terbuat</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-label-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <span class="badge bg-white text-success rounded-pill p-2"><i class="bx bx-money fs-5"></i></span>
                    </div>
                    <p class="text-muted small mb-1 fw-bold">PENDAPATAN (LUNAS)</p>
                    <h3 class="fw-bold mb-0 text-success">Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</h3>
                    <small class="text-muted border-top d-block mt-2 pt-1 border-white">Total uang masuk</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-label-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <span class="badge bg-white text-danger rounded-pill p-2"><i class="bx bx-time-five fs-5"></i></span>
                    </div>
                    <p class="text-muted small mb-1 fw-bold">BELUM TERBAYAR (PIUTANG)</p>
                    <h3 class="fw-bold mb-0 text-danger">Rp {{ number_format($summary['total_piutang'], 0, ',', '.') }}</h3>
                    <small class="text-muted border-top d-block mt-2 pt-1 border-white">Menunggu pelunasan</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bx bx-list-ul me-2 text-primary"></i> Rincian Transaksi</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">TANGGAL</th>
                            <th>ID NOTA</th>
                            <th>PELANGGAN</th>
                            <th>TOTAL</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            <tr>
                                <td class="ps-4">{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</td>
                                <td class="fw-bold">#{{ $item->id }}</td>
                                <td>
                                    {{ $item->nama_pelanggan }} <br>
                                    <small class="text-muted">{{ $item->no_wa }}</small>
                                </td>
                                <td class="fw-bold text-dark">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    @if ($item->status == 'lunas')
                                        <span class="badge bg-label-success">Lunas</span>
                                    @else
                                        <span class="badge bg-label-danger">Belum Lunas</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <img src="https://img.icons8.com/bubbles/100/000000/nothing-found.png" />
                                    <p class="mt-3 text-muted">Data transaksi pada filter ini tidak ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        @media print {
            aside, nav, .card-header, .btn-primary, .btn-outline-secondary, footer, .alert {
                display: none !important;
            }
            .content-wrapper {
                margin: 0 !important;
                padding: 0 !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            body {
                background: white !important;
            }
        }
    </style>
@endsection
