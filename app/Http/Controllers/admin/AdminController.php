<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Faktur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();
        $namaDepan = explode(' ', $admin->full_name)[0];

        // Ambil HANYA ID Sales yang terhubung dengan Admin ini
        $salesIds = $admin->sales()->pluck('id');

        // Buat Base Query Faktur khusus untuk tim Sales tersebut
        $query = Faktur::whereIn('sales_id', $salesIds);

        // Logic Filter Waktu (Hari Ini & Bulan Ini)
        $filterAktif = $request->query('filter', 'hari_ini');
        
        if ($filterAktif === 'hari_ini') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($filterAktif === 'bulan_ini') {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        }

        // Kalkulasi langsung di level Database
        $total_nota = $query->count();
        $total_tagihan = (int) $query->sum('total_tagihan');
        $total_dibayar = (int) $query->sum('total_dibayar');
        $sisa_kredit = $total_tagihan - $total_dibayar;

        // Gunakan fungsi (clone) agar filter tanggal tidak tertimpa saat menghitung status
        $lunas = (clone $query)->where('status', 'lunas')->count();
        $belum_lunas = (clone $query)->where('status', 'belum_lunas')->count();

        // Ambil 5 Riwayat Faktur Terbaru
        // Tambahkan relasi 'user' agar nanti bisa nampilin nama sales yang input nota
        $riwayat = (clone $query)->with('user')->latest()->take(5)->get();

        return view('admin.dashboard', [
            'nama_admin' => $namaDepan,
            'filter_aktif' => $filterAktif,
            'total_nota' => $total_nota,
            'total_tagihan' => $total_tagihan,
            'total_dibayar' => $total_dibayar,
            'sisa_kredit' => $sisa_kredit,
            'lunas' => $lunas,
            'belum_lunas' => $belum_lunas,
            'riwayat' => $riwayat
        ]);
    }

    // ==========================================
    // LAYER INDEX: AMBIL & FILTER LIST PEMBUKUAN
    // ==========================================
    public function pembukuanIndex(Request $request)
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();
        
        // Isolasi data ketat hanya untuk Sales yang terikat Kode Referal Admin ini
        $salesIds = $admin->sales()->pluck('id');
        $query = Faktur::whereIn('sales_id', $salesIds)->with('user');

        // Fitur Search Box (Nama Toko / No Nota)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_toko', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_faktur', 'LIKE', "%{$search}%");
            });
        }

        // Fitur Filter Waktu dari Chips
        $timeFilter = $request->query('time', 'semua');
        if ($timeFilter === 'hari_ini') {
            $query->whereDate('tanggal_nota', \Carbon\Carbon::today());
        } elseif ($timeFilter === 'minggu_ini') {
            $query->whereBetween('tanggal_nota', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
        }

        // Fitur Filter Status dari Chips
        $statusFilter = $request->query('status', 'semua');
        if ($statusFilter !== 'semua') {
            $query->where('status', $statusFilter);
        }

        // Fitur Filter Custom Date Range dari Bottom Sheet Modal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_nota', [$request->start_date, $request->end_date]);
        }

        // Hitung total finansial secara realtime berdasarkan query yang sedang ter-filter
        $total_tagihan = (int) (clone $query)->sum('total_tagihan');
        $total_dibayar = (int) (clone $query)->sum('total_dibayar');
        $sisa_tagihan = $total_tagihan - $total_dibayar;

        $fakturs = $query->latest()->get();

        return view('admin.pembukuan.index', [
            'fakturs' => $fakturs,
            'total_tagihan' => $total_tagihan,
            'sisa_tagihan' => $sisa_tagihan,
            'filter_time' => $timeFilter,
            'filter_status' => $statusFilter
        ]);
    }

    // ==========================================
    // LAYER DETAIL: TAMPILKAN SPESIFIK DATA NOTA
    // ==========================================
    public function pembukuanShow($id)
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();
        $salesIds = $admin->sales()->pluck('id');

        // Mencegah Admin luar menembak ID nota lewat URL manual
        $faktur = Faktur::whereIn('sales_id', $salesIds)->with('user')->findOrFail($id);

        return view('admin.pembukuan.show', compact('faktur'));
    }

    // ==========================================
    // TAMPILAN INDEX PROFIL ADMIN
    // ==========================================
    public function profileIndex()
    {
        $user = Auth::user();
        return view('admin.profile.index', compact('user'));
    }

    // ==========================================
    // HALAMAN FORM EDIT PROFIL
    // ==========================================
    public function profileEdit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    // ==========================================
    // PROSES UPDATE DATA PROFIL (NAMA & NO TLP)
    // ==========================================
    public function profileUpdate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'full_name'    => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20|regex:/^[0-9\s\-\+\(\)]+$/'
        ], [
            'full_name.required' => 'Nama tidak boleh kosong.',
            'phone_number.regex' => 'Format nomor telepon tidak valid.'
        ]);

        $user->update([
            'full_name'    => $request->full_name,
            'phone_number' => $request->phone_number,
        ]);

        return redirect()->route('admin.profile.index')->with('success', 'Profil berhasil diperbarui!');
    }

    // ==========================================
    // TAMPILAN HALAMAN MANAJEMEN CUSTOM CODE
    // ==========================================
    public function customCodeIndex()
    {
        $user = Auth::user();
        return view('admin.profile.custom-code', compact('user'));
    }

    // ==========================================
    // PROSES UPDATE KODE REFERAL ADMIN (CUSTOM CODE)
    // ==========================================
    public function customCodeUpdate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'admin_code' => 'required|string|max:50|alpha_dash|unique:users,admin_code,' . $user->id,
        ], [
            'admin_code.required'   => 'Kode referal tidak boleh kosong.',
            'admin_code.alpha_dash' => 'Kode hanya boleh berisi huruf, angka, dan tanda strip.',
            'admin_code.unique'     => 'Kode referal ini sudah dipakai oleh Admin lain. Cari kode lain!'
        ]);

        $user->update([
            'admin_code' => strtoupper($request->admin_code)
        ]);

        return redirect()->route('admin.profile.index')->with('success', 'Custom Code berhasil diperbarui!');
    }
}
