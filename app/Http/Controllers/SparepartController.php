<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SparepartController extends Controller
{
    public function index()
    {
        return view('sparepart.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('spareparts')
                ->where('user_id', Auth::id())
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '<div class="btn-group">
                        <button type="button" class="btn btn-sm btn-icon btn-primary editSparepart" data-id="'.$row->id.'" data-bs-toggle="modal" data-bs-target="#modalSparepart">
                            <i class="bx bx-edit-alt"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-danger deleteSparepart" data-id="'.$row->id.'">
                            <i class="bx bx-trash"></i>
                        </button>
                    </div>';
                    return $actionBtn;
                })
                ->addColumn('harga_beli_formatted', function($row){
                    return 'Rp ' . number_format($row->harga_beli, 0, ',', '.');
                })
                ->addColumn('harga_jual_formatted', function($row){
                    return 'Rp ' . number_format($row->harga_jual, 0, ',', '.');
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_sparepart' => 'required|string|max:255',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
        ]);

        DB::table('spareparts')->insert([
            'user_id' => Auth::id(),
            'nama_sparepart' => $request->nama_sparepart,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'stok' => $request->stok,
            'deskripsi' => $request->deskripsi,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => 'Sparepart berhasil disimpan!']);
    }

    public function show($id)
    {
        $sparepart = DB::table('spareparts')->where('id', $id)->where('user_id', Auth::id())->first();
        return response()->json($sparepart);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_sparepart' => 'required|string|max:255',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'stok' => 'required|integer',
        ]);

        DB::table('spareparts')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'nama_sparepart' => $request->nama_sparepart,
                'harga_beli' => $request->harga_beli,
                'harga_jual' => $request->harga_jual,
                'stok' => $request->stok,
                'deskripsi' => $request->deskripsi,
                'updated_at' => now(),
            ]);

        return response()->json(['success' => 'Sparepart berhasil diperbarui!']);
    }

    public function destroy($id)
    {
        DB::table('spareparts')->where('id', $id)->where('user_id', Auth::id())->delete();
        return response()->json(['success' => 'Sparepart berhasil dihapus!']);
    }
}
