<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\SparepartController;
use App\Http\Controllers\StokMasukController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/layanan/data', [LayananController::class, 'getData'])->name('layanan.data');
    Route::resource('layanan', LayananController::class);

    Route::get('/sparepart/data', [SparepartController::class, 'getData'])->name('sparepart.data');
    Route::resource('sparepart', SparepartController::class);

    Route::get('/stok-masuk/data', [StokMasukController::class, 'getData'])->name('stok-masuk.data');
    Route::resource('stok-masuk', StokMasukController::class);

    Route::get('/pelanggan/data', [PelangganController::class, 'getData'])->name('pelanggan.data');
    Route::resource('pelanggan', PelangganController::class);

    Route::get('/transaksi/data', [TransaksiController::class, 'getData'])->name('transaksi.data');
    Route::post('/transaksi/{id}/pembayaran', [TransaksiController::class, 'updatePayment'])->name('transaksi.pembayaran');
    Route::resource('transaksi', TransaksiController::class);
});

require __DIR__.'/auth.php';
