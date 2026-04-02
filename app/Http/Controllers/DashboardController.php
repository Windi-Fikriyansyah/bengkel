<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // Counts for Checklist
        $layananCount = DB::table('layanan')->where('user_id', $userId)->count();
        $sparepartCount = DB::table('spareparts')->where('user_id', $userId)->count();
        $transaksiCount = DB::table('transaksi')->where('user_id', $userId)->count();

        // Stats for Dashboard Cards
        $totalIncome = DB::table('transaksi')->where('user_id', $userId)->where('status', 'lunas')->sum('total_harga');
        $todayIncome = DB::table('transaksi')
            ->where('user_id', $userId)
            ->where('status', 'lunas')
            ->whereDate('created_at', now()->today())
            ->sum('total_harga');
        $customerCount = DB::table('pelanggan')->where('user_id', $userId)->count();
        $lowStockCount = DB::table('spareparts')->where('user_id', $userId)->where('stok', '<', 5)->count();

        $isChecklistComplete = ($layananCount > 0 && $sparepartCount > 0 && $transaksiCount > 0);

        return view('dashboard', compact(
            'layananCount', 'sparepartCount', 'transaksiCount', 
            'totalIncome', 'todayIncome', 'customerCount', 
            'lowStockCount', 'isChecklistComplete'
        ));
    }
}
