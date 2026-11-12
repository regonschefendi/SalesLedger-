<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\Faktur;
use App\Models\Toko;
use App\Models\User;
use App\Notifications\SystemNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OcrController extends Controller
{
    public function index()
    {
        return view('sales.ocr');
    }

    public function processApi(Request $request)
    {
        // Validasi File Gambar yang Diupload
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            $imagePath = $request->file('image')->getPathname();
            $mimeType = $request->file('image')->getMimeType();
            $base64Image = base64_encode(file_get_contents($imagePath));

            $apiKey = config('services.gemini.key');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";
            $prompt = 'Kamu adalah asisten admin data entry. Ekstrak data dari faktur ini. Cari "Nama Toko" (biasanya di samping/bawah tulisan Kepada/Penerima/Tgl). Cari "Nomor Faktur" (nomor invoice/nota). Cari "Tanggal Nota" (Tanggal faktur). Cari "Total Tagihan" (Jumlah total akhir berupa angka murni tanpa titik/koma/Rp). Kembalikan HANYA JSON.';

            $response = Http::post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $base64Image,
                                ],
                            ],
                        ],
                    ],
                ],
                // Respon Balik JSON
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ],
            ]);

            if (! $response->successful()) {
                return response()->json(['success' => false, 'message' => 'API Error: '.$response->body()], 500);
            }

            $result = $response->json();
            $jsonString = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

            $jsonString = preg_replace('/^```json\s*(.*?)\s*```$/is', '$1', trim($jsonString));

            // Parsing JSON dari Gemini
            $extractedData = json_decode($jsonString, true);

            return response()->json([
                'success' => true,
                'data' => [
                    'nama_toko' => $extractedData['Nama Toko'] ?? $extractedData['nama_toko'] ?? '',
                    'nomor_faktur' => $extractedData['Nomor Faktur'] ?? $extractedData['nomor_faktur'] ?? 'INV-'.rand(10000, 99999),
                    // 'tanggal_nota' => $extractedData['Tanggal Nota'] ?? $extractedData['tanggal_nota'] ?? '',
                    'total_tagihan' => preg_replace('/[^0-9]/', '', $extractedData['Total Tagihan'] ?? '0'),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Fungsi API untuk fitur Dropdown Search Toko di Frontend
    public function searchToko(Request $request)
    {
        /** @var User $sales */
        $sales = Auth::user();
        $keyword = $request->get('q');

        if (! $sales->parent_admin_id) {
            return response()->json([]);
        }

        // Sales hanya bisa mencari toko yang ditambahkan admin terkait.
        $tokos = Toko::where('admin_id', $sales->parent_admin_id)
            ->where('nama_toko', 'ILIKE', "%{$keyword}%")
            ->select('id', 'nama_toko', 'alamat')
            ->limit(5)
            ->get();

        return response()->json($tokos);
    }

    public function createFaktur(Request $request)
    {
        /** @var User $sales */
        $sales = Auth::user();

        if (! $sales->parent_admin_id) {
            return response()->json([
                'success' => false,
                'message' => 'Akun sales belum terhubung ke admin.',
            ], 403);
        }

        $request->validate([
            'toko_id' => [
                'required',
                Rule::exists('tokos', 'id')->where(
                    fn ($query) => $query->where('admin_id', $sales->parent_admin_id)->whereNull('deleted_at')
                ),
            ],
            'nomor_faktur' => 'required|string|max:100',
            'tanggal_nota' => 'required|string|max:100|date_format:d/m/Y',
            'total_tagihan' => 'required|numeric|min:0',
            'total_dibayar' => 'nullable|numeric|min:0',
            'metode_bayar' => 'required|in:Cash,Transfer',
        ], [
            'toko_id.exists' => 'Toko yang dipilih bukan milik admin kamu.',
        ]);

        $request['tanggal_nota'] = Carbon::createFromFormat('d/m/Y', $request->tanggal_nota)->format('Y-m-d');

        if (! $request->hasFile('foto_file')) {
            return response()->json([
                'success' => false,
                'message' => 'GAGAL: Server tidak menerima file foto. Hal ini biasanya karena ukuran file melebihi limit server (upload_max_filesize) atau proses kompresi gagal.',
            ]);
        }

        $file = $request->file('foto_file');

        // Cek kalau filenya corrupt saat diupload
        if (! $file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'GAGAL: File rusak saat diunggah. Kode error: '.$file->getErrorMessage(),
            ]);
        }

        // PROSES UPLOAD KE SUPABASE
        try {
            $fileName = time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();

            $uploadSukses = Storage::disk('supabase')->putFileAs('/', $file, $fileName, 'public');

            if ($uploadSukses) {
                $fotoUrl = config('services.supabase.url').'/storage/v1/object/public/'.config('services.supabase.bucket').'/'.$fileName;
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal mengunggah file ke Supabase. Cek Access Key di .env!']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error Server Cloud: '.$e->getMessage()]);
        }

        $totalTagihan = (int) $request->total_tagihan;
        $totalDibayar = (int) ($request->total_dibayar ?? 0);

        if ($totalDibayar > $totalTagihan) {
            $totalDibayar = $totalTagihan;
        }

        $faktur = Faktur::create([
            'toko_id' => $request->toko_id,
            'nomor_faktur' => $request->nomor_faktur,
            'tanggal_nota' => $request->tanggal_nota,
            'tanggal_pembayaran' => $totalDibayar > 0 ? Carbon::today()->toDateString() : null,
            'total_tagihan' => $totalTagihan,
            'total_dibayar' => $totalDibayar,
            'metode_bayar' => $request->metode_bayar,
            'status' => ($totalDibayar >= $totalTagihan) ? 'lunas' : 'belum_lunas',
            'sales_id' => $sales->id,
            'foto_url' => $fotoUrl,
        ]);

        // Notifikasi untuk admin pemilik toko/sales tersebut.
        $admin = $sales->parentAdmin;
        if ($admin) {
            $toko = Toko::find($faktur->toko_id);

            Notification::send(
                $admin,
                new SystemNotification(
                    'Nota Baru',
                    $sales->full_name.' menginput nota '.$faktur->nomor_faktur
                        .' untuk toko "'.($toko->nama_toko ?? '-').'".',
                    route('admin.pembukuan.show', $faktur->id),
                    'faktur'
                )
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Data faktur berhasil disimpan!',
            'foto_url' => $fotoUrl,
        ]);
    }
}
