<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TransaksiController extends Controller
{
    public function index()
    {
        return view('transaksi.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('transaksi')
                ->leftJoin('pelanggan', 'transaksi.pelanggan_id', '=', 'pelanggan.id')
                ->where('transaksi.user_id', Auth::id())
                ->select('transaksi.*', 'pelanggan.nama_pelanggan as nama_pelanggan_ref')
                ->orderBy('transaksi.created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $payBtn = $row->status != 'lunas' ? '
                        <button type="button" class="btn btn-sm btn-icon btn-success" onclick="openPaymentModal('.$row->id.', '.$row->total_harga.')" title="Pelunasan">
                            <i class="bx bx-money"></i>
                        </button>' : '';
                        
                    $actionBtn = '<div class="btn-group">' . $payBtn . '
                        <button type="button" class="btn btn-sm btn-icon btn-info" onclick="viewDetail('.$row->id.')" title="Detail">
                            <i class="bx bx-show"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-danger" onclick="deleteTransaksi('.$row->id.')" title="Hapus">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>';
                    return $actionBtn;
                })
                ->addColumn('customer', function($row){
                    return $row->pelanggan_id ? $row->nama_pelanggan_ref : $row->nama_pelanggan;
                })
                ->addColumn('total_formatted', function($row){
                    return 'Rp ' . number_format($row->total_harga, 0, ',', '.');
                })
                ->editColumn('status', function($row){
                    if ($row->status == 'lunas') {
                        return '<span class="badge bg-label-success">Lunas</span>';
                    } else {
                        return '<span class="badge bg-label-danger">Belum Lunas</span>';
                    }
                })
                ->addColumn('tanggal', function($row){
                    return date('d/m/Y H:i', strtotime($row->created_at));
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }

    public function create()
    {
        $pelanggan = DB::table('pelanggan')->where('user_id', Auth::id())->get();
        $layanan = DB::table('layanan')->where('user_id', Auth::id())->get();
        $sparepart = DB::table('spareparts')->where('user_id', Auth::id())->where('stok', '>', 0)->get();
        
        return view('transaksi.create', compact('pelanggan', 'layanan', 'sparepart'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'status' => 'required|in:lunas,pending',
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Tangani Pelanggan (Simpan ke master jika manual)
            $pelanggan_id = $request->pelanggan_id;
            
            if ($pelanggan_id == 'manual') {
                // Cek apakah No WA sudah ada untuk user ini
                $existing = DB::table('pelanggan')
                    ->where('user_id', Auth::id())
                    ->where('no_wa', $request->no_wa)
                    ->first();
                
                if ($existing) {
                    $pelanggan_id = $existing->id;
                } else {
                    $pelanggan_id = DB::table('pelanggan')->insertGetId([
                        'user_id' => Auth::id(),
                        'nama_pelanggan' => $request->nama_pelanggan,
                        'no_wa' => $request->no_wa,
                        'no_polisi' => $request->no_polisi,
                        'tipe_kendaraan' => $request->tipe_kendaraan,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            // 2. Simpan Header Transaksi
            $transaksiId = DB::table('transaksi')->insertGetId([
                'user_id' => Auth::id(),
                'pelanggan_id' => $pelanggan_id,
                'nama_pelanggan' => $request->nama_pelanggan,
                'no_wa' => $request->no_wa,
                'no_polisi' => $request->no_polisi,
                'total_harga' => $request->total_harga,
                'bayar' => $request->bayar ?? 0,
                'kembali' => $request->kembali ?? 0,
                'status' => $request->status,
                'catatan' => $request->catatan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Simpan Detail Transaksi
            foreach ($request->items as $item) {
                DB::table('transaksi_detail')->insert([
                    'transaksi_id' => $transaksiId,
                    'item_id' => $item['id'],
                    'tipe' => $item['tipe'],
                    'nama_item' => $item['nama'],
                    'harga' => $item['harga'],
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $item['subtotal'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 3. Kurangi stok jika itu sparepart
                if ($item['tipe'] == 'sparepart') {
                    DB::table('spareparts')
                        ->where('id', $item['id'])
                        ->where('user_id', Auth::id())
                        ->decrement('stok', $item['jumlah']);
                }
            }

            return response()->json([
                'success' => 'Transaksi berhasil disimpan!',
                'transaksi_id' => $transaksiId
            ]);
        });
    }

    public function show($id)
    {
        $transaksi = DB::table('transaksi')
            ->leftJoin('pelanggan', 'transaksi.pelanggan_id', '=', 'pelanggan.id')
            ->where('transaksi.id', $id)
            ->where('transaksi.user_id', Auth::id())
            ->select('transaksi.*', 'pelanggan.nama_pelanggan as nama_pelanggan_ref', 'pelanggan.no_wa as no_wa_ref', 'pelanggan.no_polisi as no_polisi_ref')
            ->first();

        if (!$transaksi) return response()->json(['error' => 'Data tidak ditemukan'], 404);

        $details = DB::table('transaksi_detail')
            ->where('transaksi_id', $id)
            ->get();

        return response()->json([
            'header' => $transaksi,
            'details' => $details
        ]);
    }

    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'bayar' => 'required|numeric|min:0',
            'kembali' => 'required|numeric',
        ]);

        DB::table('transaksi')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'status' => 'lunas',
                'bayar' => $request->bayar,
                'kembali' => $request->kembali,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => 'Pembayaran berhasil diselesaikan!']);
    }

    public function printNota($id)
    {
        $userId = Auth::id();
        $transaksi = DB::table('transaksi')->where('id', $id)->where('user_id', $userId)->first();
        if (!$transaksi) return abort(404);

        $details = DB::table('transaksi_detail')->where('transaksi_id', $id)->get();

        return view('transaksi.nota', compact('transaksi', 'details'));
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $details = DB::table('transaksi_detail')->where('transaksi_id', $id)->get();
            
            // Kembalikan stok sparepart
            foreach ($details as $item) {
                if ($item->tipe == 'sparepart' && $item->item_id) {
                    DB::table('spareparts')
                        ->where('id', $item->item_id)
                        ->where('user_id', Auth::id())
                        ->increment('stok', $item->jumlah);
                }
            }

            DB::table('transaksi')->where('id', $id)->where('user_id', Auth::id())->delete();
            return response()->json(['success' => 'Transaksi berhasil dihapus dan stok telah dikembalikan!']);
        });
    }
}
