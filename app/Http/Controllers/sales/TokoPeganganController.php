<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TokoPeganganController extends Controller
{
    public function index()
    {
        /** @var User $sales */
        $sales = Auth::user();

        if (! $sales->parent_admin_id) {
            return redirect('/select-role')->with('error', 'Lu harus masukin Kode Referal Admin dulu!');
        }

        $adminId = $sales->parent_admin_id;

        // Ambil hanya toko milik admin yang terhubung dengan sales ini.
        $assignedTokos = $sales->tokosPegangan()
            ->where('tokos.admin_id', $adminId)
            ->orderBy('nama_toko', 'asc')
            ->get();

        $assignedTokoIds = $assignedTokos->pluck('id')->toArray();

        // Halaman "Pilih Toko" juga hanya menampilkan toko dari admin terkait.
        $allTokos = Toko::where('admin_id', $adminId)
            ->orderBy('nama_toko', 'asc')
            ->get();

        return view('sales.toko-pegangan.index', compact('assignedTokos', 'allTokos', 'assignedTokoIds'));
    }

    public function sync(Request $request)
    {
        /** @var User $sales */
        $sales = Auth::user();

        if (! $sales->parent_admin_id) {
            return redirect('/select-role')->with('error', 'Lu harus masukin Kode Referal Admin dulu!');
        }

        $adminId = $sales->parent_admin_id;

        $request->validate([
            'toko_ids' => 'nullable|array',
            'toko_ids.*' => [
                'integer',
                Rule::exists('tokos', 'id')->where(
                    fn ($query) => $query->where('admin_id', $adminId)->whereNull('deleted_at')
                ),
            ],
        ], [
            'toko_ids.*.exists' => 'Ada toko yang bukan milik admin kamu.',
        ]);

        // Fungsi sync() otomatis nambah dan ngehapus data di tabel pivot.
        // Dengan validasi di atas, sales tidak bisa menyimpan toko milik admin lain.
        $sales->tokosPegangan()->sync($request->toko_ids ?? []);

        return redirect()->route('sales.toko-pegangan.index')->with('success', 'Data toko pegangan berhasil disimpan!');
    }
}
