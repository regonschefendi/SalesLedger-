<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Faktur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        // Ambil user dengan role sales (Sesuaikan dengan nama kolom role di database Anda)
        $query = User::withCount([
            'tokosPegangan as tokos_pegangan_count' => fn ($q) => $q->where('tokos.admin_id', Auth::id()),
            'fakturs as belum_lunas_count' => fn ($q) => $q->where('status', 'belum_lunas'),
        ])
            ->where('role', 'sales')
            ->where('parent_admin_id', Auth::id());

        // Fitur Pencarian
        if ($request->filled('q')) {
            $query->where('full_name', 'ILIKE', '%'.$request->q.'%');
        }

        $salesUsers = $query->get()->each(function ($sales) {
            $sales->total_toko = $sales->tokos_pegangan_count;
        });

        $totalSales = $salesUsers->count();

        return view('admin.sales.index', compact('salesUsers', 'totalSales'));
    }

    public function show($id)
    {
        $sales = User::with([
            'tokosPegangan' => fn ($q) => $q->where('tokos.admin_id', Auth::id()),
        ])
            ->where('role', 'sales')
            ->where('parent_admin_id', Auth::id())
            ->findOrFail($id);

        // Ambil faktur beserta relasi tokonya
        $fakturs = Faktur::with('toko')->where('sales_id', $sales->id)->get();

        // Hitung statistik detail sales
        $stats = [
            'total_toko' => $sales->tokosPegangan->count(),
            'total_nota' => $fakturs->count(),
            'total_tagihan' => $fakturs->sum('total_tagihan'),
            'sisa_tagihan' => $fakturs->sum(function ($f) {
                return max(0, $f->total_tagihan - $f->total_dibayar);
            }),
            'lunas_count' => $fakturs->where('status', 'lunas')->count(),
            'belum_lunas_count' => $fakturs->where('status', 'belum_lunas')->count(),
        ];

        // Kelompokkan faktur berdasarkan toko untuk list "Toko Pegangan"
        // Looping toko yang sedang dipegang dari relasi pivot
        $tokoPegangan = $sales->tokosPegangan->map(function ($toko) use ($fakturs) {
            // Cari faktur yang sesuai sama toko ini aja untuk ngitung status tagihan
            $faktursToko = $fakturs->where('toko_id', $toko->id);
            $sisa = $faktursToko->sum(function ($f) {
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
