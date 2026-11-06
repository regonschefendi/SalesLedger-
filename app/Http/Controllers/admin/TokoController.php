<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TokoRequest;
use App\Models\Faktur;
use App\Models\Toko;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TokoController extends Controller
{
    // ==========================================
    // TAMPILAN INDEX TOKO
    // ==========================================
    public function index()
{
    $salesIds = Auth::user()->sales()->pluck('id');

    $tokos = Toko::with([
        'fakturs' => fn ($q) => $q->whereIn('sales_id', $salesIds),
        'sales' => fn ($q) => $q->whereIn('users.id', $salesIds),
    ])->get()->map(function($toko) {
        
        // Kalkulasi status lunas/belum lunas dari seluruh faktur
        $totalTagihan = $toko->fakturs->sum('total_tagihan');
        $totalDibayar = $toko->fakturs->sum('total_dibayar');
        $sisa = $totalTagihan - $totalDibayar;
        
        $toko->status_pembayaran = $sisa > 0 ? 'belum_lunas' : 'lunas';

        // Cek dulu apakah toko ini punya sales (tidak null)
        $toko->sales_name = $toko->sales->isNotEmpty() ? $toko->sales->first()->full_name : 'Belum ditugaskan';
        
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
        $salesIds = Auth::user()->sales()->pluck('id');

        // Ambil data toko spesifik beserta riwayat fakturnya (hanya milik tim admin ini)
        $toko = Toko::with([
            'fakturs' => function($query) use ($salesIds) {
                $query->whereIn('sales_id', $salesIds)->orderBy('created_at', 'desc'); // Urutkan nota dari yang terbaru
            },
            'fakturs.user',
            'sales' => fn ($q) => $q->whereIn('users.id', $salesIds),
        ])->findOrFail($id);

        $salesPegangan = $toko->sales->map(function($sales) {
            return [
                'nama' => $sales->full_name,
            ];
        });

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

        return view('admin.toko.show', compact('toko', 'stats', 'salesPegangan'));
    }

    // Menampilkan halaman SPA Tambah Toko
    public function create()
    {
        return view('admin.toko.create');
    }

    // Endpoint API untuk menyimpan data via AJAX
    public function store(TokoRequest $request)
    {
        try {
            Toko::create($request->validated());
            return response()->json(['success' => true, 'message' => 'Toko berhasil ditambahkan!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data sistem.']);
        }
    }

    // Menampilkan Halaman Detail Nota Spesifik
    public function showNota($id)
    {
        $salesIds = Auth::user()->sales()->pluck('id');

        // Tarik data faktur beserta relasi toko dan sales-nya (hanya milik tim admin ini)
        $faktur = Faktur::with(['toko', 'user'])->whereIn('sales_id', $salesIds)->findOrFail($id);
        
        return view('admin.toko.detail-faktur', compact('faktur'));
    }

    // Menampilkan Form Edit
    public function edit(Toko $toko)
    {
        Gate::authorize('update', $toko); 
        
        return view('admin.toko.edit', compact('toko'));
    }

    // Memproses Update Data
    public function update(TokoRequest $request, Toko $toko)
    {
        Gate::authorize('update', $toko);

        // Data sudah otomatis divalidasi oleh TokoRequest
        $toko->update($request->validated());

        return redirect()->route('admin.toko.show', $toko->id)
                         ->with('success', 'Informasi toko berhasil diperbarui.');
    }

    // Memproses Soft Delete
    public function destroy(Toko $toko)
    {
        Gate::authorize('delete', $toko);

        $toko->delete(); // Akan mengisi kolom deleted_at berkat SoftDeletes

        return redirect()->route('admin.toko.index')
                         ->with('success', 'Toko berhasil dihapus dari daftar aktif.');
    }
}
