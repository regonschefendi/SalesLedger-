<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Faktur;
use App\Models\User;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        // Ambil user dengan role sales (Sesuaikan dengan nama kolom role di database lu)
        $query = User::withCount('tokosPegangan')->where('role', 'sales');

        // Fitur Pencarian
        if ($request->filled('q')) {
            $query->where('full_name', 'ILIKE', '%' . $request->q . '%'); 
        }

        $salesUsers = $query->get()->map(function($sales) {
            // Ambil semua faktur milik sales ini
            $fakturs = Faktur::where('sales_id', $sales->id)->get();
            
            // Hitung statistik singkat
            $sales->total_toko = $sales->tokos_pegangan_count;
            $sales->belum_lunas_count = $fakturs->where('status', 'belum_lunas')->count();
            
            return $sales;
        });

        $totalSales = $salesUsers->count();

        return view('admin.sales.index', compact('salesUsers', 'totalSales'));
    }

    public function show($id)
    {
        $sales = User::with('tokosPegangan')->where('role', 'sales')->findOrFail($id);
        
        // Ambil faktur beserta relasi tokonya
        $fakturs = Faktur::with('toko')->where('sales_id', $sales->id)->get();

        // Hitung statistik detail sales
        $stats = [
            'total_toko' => $sales->tokosPegangan->count(),
            'total_nota' => $fakturs->count(),
            'total_tagihan' => $fakturs->sum('total_tagihan'),
            'sisa_tagihan' => $fakturs->sum(function($f) { 
                return max(0, $f->total_tagihan - $f->total_dibayar); 
            }),
            'lunas_count' => $fakturs->where('status', 'lunas')->count(),
            'belum_lunas_count' => $fakturs->where('status', 'belum_lunas')->count(),
        ];

        // Kelompokkan faktur berdasarkan toko untuk list "Toko Pegangan"
        // Looping toko yang sedang dipegang dari relasi pivot
        $tokoPegangan = $sales->tokosPegangan->map(function($toko) use ($fakturs) {
            // Cari faktur yang sesuai sama toko ini aja untuk ngitung status tagihan
            $faktursToko = $fakturs->where('toko_id', $toko->id);
            $sisa = $faktursToko->sum(function($f) { 
                return max(0, $f->total_tagihan - $f->total_dibayar); 
            });
            
            return (object) [
                'id' => $toko->id,
                'nama_toko' => $toko->nama_toko,
                'total_nota' => $faktursToko->count(),
                'status' => $sisa > 0 ? 'belum_lunas' : 'lunas',
            ];
        });

        return view('admin.sales.show', compact('sales', 'stats', 'tokoPegangan'));
    }
}
