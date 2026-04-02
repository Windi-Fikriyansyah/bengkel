<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->today()->toDateString());

        $data = DB::table('transaksi')
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_transaksi' => $data->count(),
            'total_pendapatan' => $data->where('status', 'lunas')->sum('total_harga'),
            'total_piutang' => $data->where('status', 'pending')->sum('total_harga'),
        ];

        return view('laporan.index', compact('data', 'startDate', 'endDate', 'summary'));
    }
}
