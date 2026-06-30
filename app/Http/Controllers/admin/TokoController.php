<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Faktur;
use App\Models\Toko;
use Illuminate\Http\Request;

class TokoController extends Controller
{
    // ==========================================
    // TAMPILAN INDEX TOKO
    // ==========================================
    public function index()
    {
        // Ambil semua toko beserta relasi faktur dan user (sales)
        $tokos = Toko::with(['fakturs.user'])->get()->map(function($toko) {
            // Kalkulasi status lunas/belum lunas dari seluruh faktur toko ini
            $totalTagihan = $toko->fakturs->sum('total_tagihan');
            $totalDibayar = $toko->fakturs->sum('total_dibayar');
            $sisa = $totalTagihan - $totalDibayar;
            
            $toko->status_pembayaran = $sisa > 0 ? 'belum_lunas' : 'lunas';
            
            // Ambil nama sales terakhir yang menangani toko ini
            $latestFaktur = $toko->fakturs->sortByDesc('created_at')->first();
            $toko->sales_name = $latestFaktur && $latestFaktur->user ? $latestFaktur->user->full_name : 'Belum ada';
            
            return $toko;
        });

        // Hitung statistik global untuk kartu di atas list
        $stats = [
            'total' => $tokos->count(),
            'belum_lunas' => $tokos->where('status_pembayaran', 'belum_lunas')->count(),
        ];

        return view('admin.toko.index', compact('tokos', 'stats'));
    }

    // ==========================================
    // MENAMPILKAN HALAMAN DETAIL TOKO (SHOW)
    // ==========================================
    public function show($id)
    {
        // Ambil data toko spesifik beserta riwayat fakturnya
        $toko = Toko::with(['fakturs' => function($query) {
            $query->orderBy('created_at', 'desc'); // Urutkan nota dari yang terbaru
        }, 'fakturs.user'])->findOrFail($id);

        // Kalkulasi Statistik Keuangan Toko
        $totalTagihan = $toko->fakturs->sum('total_tagihan');
        $totalDibayar = $toko->fakturs->sum('total_dibayar');
        
        $stats = [
            'total_nota' => $toko->fakturs->count(),
            'total_tagihan' => $totalTagihan,
            'sudah_dibayar' => $totalDibayar,
            'sisa_tagihan' => max(0, $totalTagihan - $totalDibayar),
        ];

        // Dapatkan nama sales terakhir untuk ditampilkan di header profil toko
        $latestFaktur = $toko->fakturs->first();
        $toko->sales_name = $latestFaktur && $latestFaktur->user ? $latestFaktur->user->full_name : 'Belum ditugaskan';

        return view('admin.toko.show', compact('toko', 'stats'));
    }

    // Menampilkan halaman SPA Tambah Toko
    public function create()
    {
        return view('admin.toko.create');
    }

    // Endpoint API untuk menyimpan data via AJAX
    public function store(Request $request)
    {
        // Validasi dan Security
        $validated = $request->validate([
            'nama_toko' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'provinsi' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
        ]);

        try {
            Toko::create($validated);
            return response()->json(['success' => true, 'message' => 'Toko berhasil ditambahkan!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data sistem.']);
        }
    }

    // Menampilkan Halaman Detail Nota Spesifik
    public function showNota($id)
    {
        // Tarik data faktur beserta relasi toko dan sales-nya
        $faktur = Faktur::with(['toko', 'user'])->findOrFail($id);
        
        return view('admin.toko.detail-faktur', compact('faktur'));
    }
}
