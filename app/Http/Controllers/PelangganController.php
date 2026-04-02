<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PelangganController extends Controller
{
    public function index()
    {
        return view('pelanggan.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('pelanggan')
                ->where('user_id', Auth::id())
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '<div class="btn-group">
                        <button type="button" class="btn btn-sm btn-icon btn-info viewHistory" data-id="'.$row->id.'" title="History Service">
                            <i class="bx bx-history"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-primary editPelanggan" data-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#modalPelanggan">
                            <i class="bx bx-edit-alt"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-danger deletePelanggan" data-id="'.$row->id.'">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'no_wa' => 'required|string|max:20',
        ]);

        DB::table('pelanggan')->insert([
            'user_id' => Auth::id(),
            'nama_pelanggan' => $request->nama_pelanggan,
            'no_wa' => $request->no_wa,
            'no_polisi' => $request->no_polisi,
            'tipe_kendaraan' => $request->tipe_kendaraan,
            'alamat' => $request->alamat,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => 'Data pelanggan berhasil disimpan!']);
    }

    public function show($id)
    {
        $pelanggan = DB::table('pelanggan')->where('id', $id)->where('user_id', Auth::id())->first();
        
        if (!$pelanggan) return response()->json(['error' => 'Data tidak ditemukan'], 404);

        $history = DB::table('transaksi')
            ->where('pelanggan_id', $id)
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'pelanggan' => $pelanggan,
            'history' => $history
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'no_wa' => 'required|string|max:20',
        ]);

        DB::table('pelanggan')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'nama_pelanggan' => $request->nama_pelanggan,
                'no_wa' => $request->no_wa,
                'no_polisi' => $request->no_polisi,
                'tipe_kendaraan' => $request->tipe_kendaraan,
                'alamat' => $request->alamat,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => 'Data pelanggan berhasil diperbarui!']);
    }

    public function destroy($id)
    {
        DB::table('pelanggan')->where('id', $id)->where('user_id', Auth::id())->delete();
        return response()->json(['success' => 'Data pelanggan berhasil dihapus!']);
    }
}
