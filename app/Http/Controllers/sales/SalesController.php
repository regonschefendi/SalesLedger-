<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\Faktur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        if (! $user->parent_admin_id) {
            return redirect('/select-role')->with('error', 'Anda harus memasukkan Kode Referal Admin terlebih dahulu!');
        }

        // Satu query: 5 riwayat terbaru + agregat seluruh faktur lewat window function.
        $riwayat = Faktur::where('fakturs.sales_id', $user->id)
            ->leftJoin('tokos', function ($join) {
                $join->on('tokos.id', '=', 'fakturs.toko_id')->whereNull('tokos.deleted_at');
            })
            ->select('fakturs.*', 'tokos.nama_toko as toko_name')
            ->selectRaw('count(fakturs.id) over () as stat_total_nota')
            ->selectRaw('coalesce(sum(fakturs.total_tagihan) over (), 0) as stat_total_tagihan')
            ->selectRaw('coalesce(sum(fakturs.total_dibayar) over (), 0) as stat_total_dibayar')
            ->selectRaw("coalesce(sum(case when fakturs.status = 'lunas' then 1 else 0 end) over (), 0) as stat_lunas")
            ->selectRaw("coalesce(sum(case when fakturs.status = 'belum_lunas' then 1 else 0 end) over (), 0) as stat_belum_lunas")
            ->selectSub(function ($notificationQuery) use ($user) {
                $notificationQuery->from('notifications')
                    ->selectRaw('count(*)')
                    ->where('notifiable_type', $user->getMorphClass())
                    ->where('notifiable_id', $user->getKey())
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
            $unread_notifications = $user->unreadNotifications()->count();
        } else {
            $summary = $riwayat->first();
            $total_nota = (int) $summary->stat_total_nota;
            $total_tagihan = (float) $summary->stat_total_tagihan;
            $total_dibayar = (float) $summary->stat_total_dibayar;
            $lunas = (int) $summary->stat_lunas;
            $belum_lunas = (int) $summary->stat_belum_lunas;
            $unread_notifications = (int) $summary->unread_notifications;
        }

        $sisa_kredit = $total_tagihan - $total_dibayar;

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
            'riwayat' => $riwayat,
            'unread_notifications' => $unread_notifications,
        ];

        return view('sales.home', $data);
    }

    public function profileIndex()
    {
        /** @var User $user */
        $user = Auth::user();

        // Hitung semua statistik profil dalam satu query agar tidak bolak-balik ke database.
        $summary = Faktur::where('sales_id', $user->id)
            ->selectRaw("
                count(*) as total_nota,
                coalesce(sum(total_tagihan), 0) as total_tagihan,
                sum(case when status = 'belum_lunas' then 1 else 0 end) as belum_lunas
            ")
            ->selectSub(function ($tokoQuery) use ($user) {
                $tokoQuery->from('sales_tokos')
                    ->join('tokos', 'tokos.id', '=', 'sales_tokos.toko_id')
                    ->where('sales_tokos.user_id', $user->id)
                    ->where('tokos.admin_id', $user->parent_admin_id)
                    ->whereNull('tokos.deleted_at')
                    ->selectRaw('count(*)');
            }, 'total_toko')
            ->first();

        $stats = [
            'total_toko' => (int) $summary->total_toko,
            'total_nota' => (int) $summary->total_nota,
            'total_tagihan' => (int) $summary->total_tagihan,
            'belum_lunas' => (int) $summary->belum_lunas,
        ];

        return view('sales.profile.index', compact('user', 'stats'));
    }

    public function profileEdit()
    {
        $user = Auth::user();

        return view('sales.profile.edit', compact('user'));
    }

    public function profileUpdate(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user->update($request->only('full_name', 'phone_number'));

        return redirect()->route('sales.profile.index')->with('success', 'Profil berhasil diperbarui!');
    }
}
