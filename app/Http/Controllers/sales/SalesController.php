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
            return redirect('/select-role')->with('error', 'Lu harus masukin Kode Referal Admin dulu!');
        }

        $fakturs = Faktur::where('sales_id', $user->id)->get();

        // Kalkulasi Data Dashboard
        $total_nota = $fakturs->count();
        $total_tagihan = $fakturs->sum('total_tagihan');
        $total_dibayar = $fakturs->sum('total_dibayar');
        $sisa_kredit = $total_tagihan - $total_dibayar;

        $lunas = $fakturs->where('status', 'lunas')->count();
        $belum_lunas = $fakturs->where('status', 'belum_lunas')->count();

        // Ambil 5 riwayat terbaru untuk list bawah
        $riwayat = Faktur::with('toko')->where('sales_id', $user->id)->latest()->take(5)->get();

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
            'unread_notifications' => $user->unreadNotifications()->count(),
        ];

        return view('sales.home', $data);
    }

    public function profileIndex()
    {
        /** @var User $user */
        $user = Auth::user();

        // Hitung statistik langsung dari database
        $stats = [
            // Hitung toko pegangan yang memang milik admin terkait
            'total_toko' => $user->tokosPegangan()
                ->where('tokos.admin_id', $user->parent_admin_id)
                ->count(),
            // Hitung total nota yang pernah dikirim
            'total_nota' => Faktur::where('sales_id', $user->id)->count(),
            // Hitung total nominal uang dari semua nota
            'total_tagihan' => (int) Faktur::where('sales_id', $user->id)->sum('total_tagihan'),
            // Hitung berapa nota yang statusnya belum lunas
            'belum_lunas' => Faktur::where('sales_id', $user->id)->where('status', 'belum_lunas')->count(),
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
