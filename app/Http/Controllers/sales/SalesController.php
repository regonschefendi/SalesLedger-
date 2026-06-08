<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\Faktur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $fakturs = Faktur::where('sales_id', $user->id)->get();

        // Kalkulasi Data Dashboard
        $total_nota = $fakturs->count();
        $total_tagihan = $fakturs->sum('total_tagihan');
        $total_dibayar = $fakturs->sum('total_dibayar');
        $sisa_kredit = $total_tagihan - $total_dibayar;
        
        $lunas = $fakturs->where('status', 'lunas')->count();
        $belum_lunas = $fakturs->where('status', 'belum_lunas')->count();

        // Ambil 5 riwayat terbaru untuk list bawah
        $riwayat = Faktur::where('sales_id', $user->id)->latest()->take(5)->get();

        // Ambil nama depan saja untuk sapaan
        $namaDepan = explode(' ', $user->full_name)[0];

        $data = [
            'nama_sales' => $namaDepan,
            'total_nota' => $total_nota,
            'total_tagihan' => $total_tagihan,
            'total_dibayar' => $total_dibayar,
            'sisa_kredit' => $sisa_kredit,
            'lunas' => $lunas,
            'belum_lunas' => $belum_lunas,
            'riwayat' => $riwayat
        ];

        return view('sales.home', $data);
    }
}
