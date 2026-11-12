<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TokoRequest;
use App\Models\Faktur;
use App\Models\Toko;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class TokoController extends Controller
{
    // ==========================================
    // TAMPILAN INDEX TOKO
    // ==========================================
    public function index()
    {
        /** @var User $admin */
        $admin = Auth::user();

        // Admin hanya boleh melihat toko yang dia tambahkan sendiri.
        $tokos = Toko::ownedByAdmin($admin)
            ->withSum(['fakturs as total_tagihan_sum' => fn ($q) => $q->forAdmin($admin)], 'total_tagihan')
            ->withSum(['fakturs as total_dibayar_sum' => fn ($q) => $q->forAdmin($admin)], 'total_dibayar')
            ->addSelect([
                'sales_name' => User::query()
                    ->select('users.full_name')
                    ->join('sales_tokos', 'sales_tokos.user_id', '=', 'users.id')
                    ->whereColumn('sales_tokos.toko_id', 'tokos.id')
                    ->where('users.parent_admin_id', $admin->id)
                    ->orderBy('users.id')
                    ->limit(1),
            ])
            ->get()->map(function ($toko) {

                // Kalkulasi status lunas/belum lunas dari seluruh faktur
                $totalTagihan = (float) $toko->total_tagihan_sum;
                $totalDibayar = (float) $toko->total_dibayar_sum;
                $sisa = $totalTagihan - $totalDibayar;

                $toko->status_pembayaran = $sisa > 0 ? 'belum_lunas' : 'lunas';

                $toko->sales_name = $toko->sales_name ?: 'Belum ditugaskan';

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
        /** @var User $admin */
        $admin = Auth::user();

        // Ambil data toko spesifik beserta riwayat fakturnya (hanya milik admin ini)
        $toko = Toko::ownedByAdmin($admin)->with([
            'fakturs' => fn ($query) => $query->forAdmin($admin)->orderBy('created_at', 'desc'),
            'fakturs.user',
            'sales' => fn ($q) => $q->whereIn(
                'users.id',
                User::where('parent_admin_id', $admin->id)->select('id')
            ),
        ])->findOrFail($id);

        $salesPegangan = $toko->sales->map(function ($sales) {
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
            /** @var User $admin */
            $admin = Auth::user();

            $toko = Toko::create([
                ...$request->validated(),
                'admin_id' => $admin->id,
            ]);

            // Beri tahu semua sales di bawah admin ini kalau ada toko baru.
            Notification::send(
                $admin->sales()->get(),
                new SystemNotification(
                    'Toko Baru',
                    $admin->full_name.' menambahkan toko "'.$toko->nama_toko.'".',
                    route('sales.toko-pegangan.index'),
                    'toko'
                )
            );

            return response()->json(['success' => true, 'message' => 'Toko berhasil ditambahkan!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data sistem.']);
        }
    }

    // Menampilkan Halaman Detail Nota Spesifik
    public function showNota($id)
    {
        /** @var User $admin */
        $admin = Auth::user();

        // Tarik data faktur beserta relasi toko dan sales-nya (hanya milik tim admin ini)
        $faktur = Faktur::forAdmin($admin)->with(['toko', 'user'])->findOrFail($id);

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

        // Beri tahu sales yang sedang memegang toko ini.
        $salesIds = Auth::user()->salesIds();
        $pemegang = $toko->sales()->whereIn('users.id', $salesIds)->get();

        if ($pemegang->isNotEmpty()) {
            Notification::send(
                $pemegang,
                new SystemNotification(
                    'Informasi Toko Diperbarui',
                    'Data toko "'.$toko->nama_toko.'" telah diperbarui oleh admin.',
                    route('sales.toko-pegangan.index'),
                    'toko'
                )
            );
        }

        return redirect()->route('admin.toko.show', $toko->id)
            ->with('success', 'Informasi toko berhasil diperbarui.');
    }

    // Memproses Soft Delete
    public function destroy(Toko $toko)
    {
        Gate::authorize('delete', $toko);

        // Beri tahu sales yang memegang toko ini sebelum toko dihapus.
        $salesIds = Auth::user()->salesIds();
        $pemegang = $toko->sales()->whereIn('users.id', $salesIds)->get();

        if ($pemegang->isNotEmpty()) {
            Notification::send(
                $pemegang,
                new SystemNotification(
                    'Toko Dihapus',
                    'Toko "'.$toko->nama_toko.'" telah dihapus oleh admin.',
                    route('sales.toko-pegangan.index'),
                    'toko'
                )
            );
        }

        $toko->delete(); // Akan mengisi kolom deleted_at berkat SoftDeletes

        return redirect()->route('admin.toko.index')
            ->with('success', 'Toko berhasil dihapus dari daftar aktif.');
    }
}
