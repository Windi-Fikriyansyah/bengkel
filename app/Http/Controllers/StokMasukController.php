<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class StokMasukController extends Controller
{
    public function index()
    {
        $spareparts = DB::table('spareparts')->where('user_id', Auth::id())->get();
        return view('stok_masuk.index', compact('spareparts'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('stok_masuk')
                ->join('spareparts', 'stok_masuk.sparepart_id', '=', 'spareparts.id')
                ->where('stok_masuk.user_id', Auth::id())
                ->select('stok_masuk.*', 'spareparts.nama_sparepart')
                ->orderBy('stok_masuk.created_at', 'desc')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '<div class="btn-group">
                        <button type="button" class="btn btn-sm btn-icon btn-danger deleteStokMasuk" data-id="'.$row->id.'">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>';
                    return $actionBtn;
                })
                ->addColumn('tanggal', function($row){
                    return date('d/m/Y H:i', strtotime($row->created_at));
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'sparepart_id' => 'required',
            'jumlah' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            // Catat log stok masuk
            DB::table('stok_masuk')->insert([
                'user_id' => Auth::id(),
                'sparepart_id' => $request->sparepart_id,
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update stok di master sparepart
            DB::table('spareparts')
                ->where('id', $request->sparepart_id)
                ->where('user_id', Auth::id())
                ->increment('stok', $request->jumlah);

            return response()->json(['success' => 'Stok berhasil ditambahkan!']);
        });
    }

    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $log = DB::table('stok_masuk')->where('id', $id)->where('user_id', Auth::id())->first();
            
            if ($log) {
                // Kurangi stok di master sparepart karena transaksi dibatalkan/dihapus
                DB::table('spareparts')
                    ->where('id', $log->sparepart_id)
                    ->where('user_id', Auth::id())
                    ->decrement('stok', $log->jumlah);

                DB::table('stok_masuk')->where('id', $id)->delete();
                return response()->json(['success' => 'Riwayat berhasil dihapus dan stok telah disesuaikan!']);
            }

            return response()->json(['error' => 'Data tidak ditemukan!'], 404);
        });
    }
}
