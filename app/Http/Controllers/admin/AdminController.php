<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Faktur;
use App\Models\User;
use App\Support\XlsxWriter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        /** @var User $admin */
        $admin = Auth::user();
        $namaDepan = explode(' ', $admin->full_name)[0];

        // Query faktur hanya untuk sales yang terhubung dengan admin ini.
        $query = Faktur::forAdmin($admin);

        // Logic Filter Waktu (Hari Ini & Bulan Ini)
        $filterAktif = $request->query('filter', 'hari_ini');

        if ($filterAktif === 'hari_ini') {
            $query->whereDate('fakturs.created_at', Carbon::today());
        } elseif ($filterAktif === 'bulan_ini') {
            $query->whereMonth('fakturs.created_at', Carbon::now()->month)
                ->whereYear('fakturs.created_at', Carbon::now()->year);
        }

        // Satu query: 5 riwayat terbaru + agregat seluruh data terfilter lewat window function.
        $riwayat = (clone $query)
            ->leftJoin('users', 'users.id', '=', 'fakturs.sales_id')
            ->leftJoin('tokos', function ($join) {
                $join->on('tokos.id', '=', 'fakturs.toko_id')->whereNull('tokos.deleted_at');
            })
            ->select('fakturs.*', 'users.full_name as sales_name', 'tokos.nama_toko as toko_name')
            ->selectRaw('count(fakturs.id) over () as stat_total_nota')
            ->selectRaw('coalesce(sum(fakturs.total_tagihan) over (), 0) as stat_total_tagihan')
            ->selectRaw('coalesce(sum(fakturs.total_dibayar) over (), 0) as stat_total_dibayar')
            ->selectRaw("coalesce(sum(case when fakturs.status = 'lunas' then 1 else 0 end) over (), 0) as stat_lunas")
            ->selectRaw("coalesce(sum(case when fakturs.status = 'belum_lunas' then 1 else 0 end) over (), 0) as stat_belum_lunas")
            ->selectSub(function ($notificationQuery) use ($admin) {
                $notificationQuery->from('notifications')
                    ->selectRaw('count(*)')
                    ->where('notifiable_type', $admin->getMorphClass())
                    ->where('notifiable_id', $admin->getKey())
                    ->whereNull('read_at');
            }, 'unread_notifications')
            ->latest('fakturs.created_at')
            ->take(5)
            ->get();

        if ($riwayat->isEmpty()) {
            $total_nota = 0;
            $total_tagihan = 0;
            $total_dibayar = 0;
            $lunas = 0;
            $belum_lunas = 0;
            $unread_notifications = $admin->unreadNotifications()->count();
        } else {
            $summary = $riwayat->first();
            $total_nota = (int) $summary->stat_total_nota;
            $total_tagihan = (int) $summary->stat_total_tagihan;
            $total_dibayar = (int) $summary->stat_total_dibayar;
            $lunas = (int) $summary->stat_lunas;
            $belum_lunas = (int) $summary->stat_belum_lunas;
            $unread_notifications = (int) $summary->unread_notifications;
        }

        $sisa_kredit = $total_tagihan - $total_dibayar;

        return view('admin.dashboard', [
            'nama_admin' => $namaDepan,
            'filter_aktif' => $filterAktif,
            'total_nota' => $total_nota,
            'total_tagihan' => $total_tagihan,
            'total_dibayar' => $total_dibayar,
            'sisa_kredit' => $sisa_kredit,
            'lunas' => $lunas,
            'belum_lunas' => $belum_lunas,
            'riwayat' => $riwayat,
            'unread_notifications' => $unread_notifications,
        ]);
    }

    // ==========================================
    // LAYER INDEX: AMBIL & FILTER LIST PEMBUKUAN
    // ==========================================
    public function pembukuanIndex(Request $request)
    {
        /** @var User $admin */
        $admin = Auth::user();

        // Isolasi data ketat hanya untuk sales yang terikat kode referal admin ini.
        $query = Faktur::forAdmin($admin);

        // Fitur Search Box (Nama Toko / No Nota)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_faktur', 'ILIKE', "%{$search}%")
                    ->orWhereHas('toko', function ($qToko) use ($search) {
                        $qToko->where('nama_toko', 'ILIKE', "%{$search}%");
                    });
            });
        }

        // Fitur Filter Waktu dari Chips
        $timeFilter = $request->query('time', 'semua');
        if ($timeFilter === 'hari_ini') {
            $query->whereDate('tanggal_nota', Carbon::today());
        } elseif ($timeFilter === 'minggu_ini') {
            $query->whereBetween('tanggal_nota', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
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

        $fakturs = $query
            ->leftJoin('users', 'users.id', '=', 'fakturs.sales_id')
            ->leftJoin('tokos', function ($join) {
                $join->on('tokos.id', '=', 'fakturs.toko_id')->whereNull('tokos.deleted_at');
            })
            ->select('fakturs.*', 'users.full_name as sales_name', 'tokos.nama_toko as toko_name')
            ->latest('fakturs.created_at')
            ->get();

        // Hitung total dari data yang sudah diambil agar tidak menambah roundtrip database.
        $total_tagihan = (int) $fakturs->sum('total_tagihan');
        $sisa_tagihan = $total_tagihan - (int) $fakturs->sum('total_dibayar');

        return view('admin.pembukuan.index', [
            'fakturs' => $fakturs,
            'total_tagihan' => $total_tagihan,
            'sisa_tagihan' => $sisa_tagihan,
            'filter_time' => $timeFilter,
            'filter_status' => $statusFilter,
        ]);
    }

    // ==========================================
    // LAYER EXPORT: DOWNLOAD PEMBUKUAN KE EXCEL
    // ==========================================
    public function pembukuanExport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Format tanggal mulai tidak valid.',
            'end_date.required' => 'Tanggal akhir wajib diisi.',
            'end_date.date' => 'Format tanggal akhir tidak valid.',
            'end_date.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal mulai.',
        ]);

        /** @var User $admin */
        $admin = Auth::user();

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        $fakturs = Faktur::forAdmin($admin)
            ->with(['user', 'toko'])
            ->whereBetween('tanggal_nota', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('tanggal_nota')
            ->get();

        if ($fakturs->isEmpty()) {
            return redirect()
                ->route('admin.pembukuan.index')
                ->with('error', 'Tidak ada data faktur pada rentang tanggal yang dipilih.');
        }

        $rows = [];
        $totalTagihan = 0;
        $totalDibayar = 0;

        foreach ($fakturs as $index => $faktur) {
            $sisa = (float) $faktur->total_tagihan - (float) $faktur->total_dibayar;
            $totalTagihan += (float) $faktur->total_tagihan;
            $totalDibayar += (float) $faktur->total_dibayar;

            $rows[] = [
                $index + 1,
                $faktur->nomor_faktur ?? '-',
                $faktur->tanggal_nota ? Carbon::parse($faktur->tanggal_nota)->format('d-m-Y') : '-',
                $faktur->toko->nama_toko ?? '-',
                $faktur->user->full_name ?? '-',
                $faktur->metode_bayar ?? '-',
                (float) $faktur->total_tagihan,
                (float) $faktur->total_dibayar,
                $sisa,
                $faktur->status === 'lunas' ? 'Lunas' : 'Belum Lunas',
                $faktur->tanggal_pembayaran ? Carbon::parse($faktur->tanggal_pembayaran)->format('d-m-Y') : '-',
            ];
        }

        $content = XlsxWriter::make(
            'Pembukuan',
            'Laporan Pembukuan '.$startDate->format('d/m/Y').' - '.$endDate->format('d/m/Y'),
            [
                'No',
                'Nomor Faktur',
                'Tanggal Nota',
                'Nama Toko',
                'Sales',
                'Metode Bayar',
                'Total Tagihan',
                'Total Dibayar',
                'Sisa Tagihan',
                'Status',
                'Tanggal Setor',
            ],
            $rows,
            [5, 20, 14, 26, 22, 16, 17, 17, 17, 14, 14],
            [null, null, null, null, null, 'TOTAL', $totalTagihan, $totalDibayar, $totalTagihan - $totalDibayar, null, null]
        );

        $filename = 'Pembukuan_'.$startDate->format('Ymd').'_sd_'.$endDate->format('Ymd').'.xlsx';

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ==========================================
    // LAYER DETAIL: TAMPILKAN SPESIFIK DATA NOTA
    // ==========================================
    public function pembukuanShow($id)
    {
        /** @var User $admin */
        $admin = Auth::user();

        // Mencegah admin luar menembak ID nota lewat URL manual.
        $faktur = Faktur::forAdmin($admin)->with(['user', 'toko'])->findOrFail($id);

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
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20|regex:/^[0-9\s\-\+\(\)]+$/',
        ], [
            'full_name.required' => 'Nama tidak boleh kosong.',
            'phone_number.regex' => 'Format nomor telepon tidak valid.',
        ]);

        $user->update([
            'full_name' => $request->full_name,
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
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'admin_code' => 'required|string|max:50|alpha_dash|unique:users,admin_code,'.$user->id,
        ], [
            'admin_code.required' => 'Kode referal tidak boleh kosong.',
            'admin_code.alpha_dash' => 'Kode hanya boleh berisi huruf, angka, dan tanda strip.',
            'admin_code.unique' => 'Kode referal ini sudah dipakai oleh Admin lain. Cari kode lain!',
        ]);

        $user->update([
            'admin_code' => strtoupper($request->admin_code),
        ]);

        return redirect()->route('admin.profile.index')->with('success', 'Custom Code berhasil diperbarui!');
    }
}
