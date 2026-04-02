<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class LayananController extends Controller
{
    public function index()
    {
        return view('layanan.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('layanan')
                ->where('user_id', Auth::id())
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '<div class="btn-group">
                        <button type="button" class="btn btn-sm btn-icon btn-primary editLayanan" data-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#modalLayanan">
                            <i class="bx bx-edit-alt"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-danger deleteLayanan" data-id="'.$row->id.'">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>';
                    return $actionBtn;
                })
                ->addColumn('harga_formatted', function($row){
                    return 'Rp ' . number_format($row->harga, 0, ',', '.');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'harga' => 'required|numeric',
        ]);

        DB::table('layanan')->insert([
            'user_id' => Auth::id(),
            'nama_layanan' => $request->nama_layanan,
            'harga' => $request->harga,
            'deskripsi' => $request->deskripsi,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => 'Layanan berhasil disimpan!']);
    }

    public function show($id)
    {
        $layanan = DB::table('layanan')->where('id', $id)->where('user_id', Auth::id())->first();
        return response()->json($layanan);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'harga' => 'required|numeric',
        ]);

        DB::table('layanan')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'nama_layanan' => $request->nama_layanan,
                'harga' => $request->harga,
                'deskripsi' => $request->deskripsi,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => 'Layanan berhasil diperbarui!']);
    }

    public function destroy($id)
    {
        DB::table('layanan')->where('id', $id)->where('user_id', Auth::id())->delete();
        return response()->json(['success' => 'Layanan berhasil dihapus!']);
    }
}
