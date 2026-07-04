<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokoPeganganController extends Controller
{
    public function index()
    {
        $sales = Auth::user();
        
        // Ambil toko yang udah dipegang
        $assignedTokos = $sales->tokosPegangan()->orderBy('nama_toko', 'asc')->get();
        $assignedTokoIds = $assignedTokos->pluck('id')->toArray();
        
        // Ambil SEMUA toko untuk halaman "Pilih Toko"
        $allTokos = Toko::orderBy('nama_toko', 'asc')->get();

        return view('sales.toko-pegangan.index', compact('assignedTokos', 'allTokos', 'assignedTokoIds'));
    }

    public function sync(Request $request)
    {
        $request->validate([
            'toko_ids' => 'nullable|array',
            'toko_ids.*' => 'exists:tokos,id'
        ]);

        // Fungsi sync() otomatis nambah dan ngehapus data di tabel pivot
        Auth::user()->tokosPegangan()->sync($request->toko_ids ?? []);

        return redirect()->route('sales.toko-pegangan.index')->with('success', 'Data toko pegangan berhasil disimpan!');
    }
}
