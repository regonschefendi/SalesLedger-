<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\Faktur;
use Carbon\Carbon;
use Illuminate\Http\Request;;
use Illuminate\Support\Facades\Auth;

class SalesRiwayatController extends Controller
{
    public function index(Request $request)
    {
        $query = Faktur::with('toko')->where('sales_id', Auth::id())->orderBy('created_at', 'desc');

        // Filter search bar
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('nomor_faktur', 'ILIKE', "%{$search}%")
                  ->orWhereHas('toko', function($qToko) use ($search) {
                      $qToko->where('nama_toko', 'ILIKE', "%{$search}%");
                  });
            });
        }

        // Filter status (Lunas / Belum Lunas)
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        $fakturs = $query->get();

        // Mengelompokkan berdasarkan tanggal untuk UI
        $groupedFakturs = $fakturs->groupBy(function($faktur) {
            $date = Carbon::parse($faktur->created_at)->startOfDay();
            if ($date->isToday()) return 'Hari ini';
            if ($date->isYesterday()) return 'Kemarin';
            return $date->translatedFormat('d F Y');
        });

        return view('sales.riwayat.index', compact('groupedFakturs'));
    }

    public function show($id)
    {
        // Pastikan hanya bisa lihat faktur miliknya sendiri
        $faktur = Faktur::with('toko')->where('sales_id', Auth::id())->findOrFail($id);
        
        return view('sales.riwayat.show', compact('faktur'));
    }

    // public function detail($id)
    // {
    //     $faktur = Faktur::with('toko')->where('sales_id', Auth::id())->findOrFail($id);
    //     return view('sales.riwayat.detail', compact('faktur'));
    // }

    public function editPembayaran($id)
    {
        $faktur = Faktur::with('toko')->where('sales_id', Auth::id())->findOrFail($id);
        
        // Mencegah akses ke faktur yang sudah lunas
        if ($faktur->status === 'lunas') {
            return redirect()->route('sales.riwayat.show', $id)->with('error', 'Faktur sudah lunas.');
        }

        return view('sales.riwayat.edit', compact('faktur'));
    }

    public function updatePembayaran(Request $request, $id)
    {
        $faktur = Faktur::where('sales_id', Auth::id())->findOrFail($id);

        $request->validate([
            'tanggal_pembayaran' => 'required|date',
            'nominal_pembayaran' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|string',
        ]);

        // Hitung total dibayar yang baru (Pembayaran lama + Nominal baru)
        $totalDibayarBaru = $faktur->total_dibayar + $request->nominal_pembayaran;
        
        // Cegah kelebihan bayar
        if ($totalDibayarBaru > $faktur->total_tagihan) {
            $totalDibayarBaru = $faktur->total_tagihan;
        }

        // Tentukan status
        $statusBaru = ($totalDibayarBaru >= $faktur->total_tagihan) ? 'lunas' : 'belum_lunas';

        $faktur->update([
            'total_dibayar' => $totalDibayarBaru,
            'metode_bayar' => $request->metode_pembayaran,
            'tanggal_pembayaran' => $request->tanggal_pembayaran,
            'status' => $statusBaru,
        ]);

        // Redirect ke halaman khusus success update
        return view('sales.riwayat.success-update', compact('faktur'));
    }
}
