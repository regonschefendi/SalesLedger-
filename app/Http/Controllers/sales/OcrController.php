<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\Faktur;
use App\Models\Toko;
use App\Models\User;
use App\Notifications\SystemNotification;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        // Terima semua format gambar (termasuk WEBP/HEIC dari HP) karena file
        // sudah dikompres menjadi JPEG di sisi client sebelum dikirim.
        $request->validate([
            'image' => [
                'required',
                'file',
                'max:10240',
                function (string $attribute, $value, \Closure $fail) {
                    $mime = (string) $value->getMimeType();

                    if (! str_starts_with($mime, 'image/')) {
                        $fail('File yang diunggah bukan gambar.');
                    }
                },
            ],
        ]);

        $apiKey = config('services.gemini.key');

        if (blank($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi GEMINI_API_KEY belum diatur di server.',
            ], 503);
        }

        try {
            $imagePath = $request->file('image')->getPathname();
            $mimeType = $request->file('image')->getMimeType();
            $base64Image = base64_encode(file_get_contents($imagePath));

            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";
            $prompt = 'Kamu adalah asisten admin data entry. Ekstrak data dari faktur ini. Cari "Nama Toko" (biasanya di samping/bawah tulisan Kepada/Penerima/Tgl). Cari "Nomor Faktur" (nomor invoice/nota). Cari "Tanggal Nota" (Tanggal faktur). Cari "Total Tagihan" (Jumlah total akhir berupa angka murni tanpa titik/koma/Rp). Kembalikan HANYA JSON.';

            $response = Http::connectTimeout(10)
                ->timeout(60)
                ->post($url, [
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
                Log::warning('Gemini OCR gagal.', ['status' => $response->status(), 'body' => $response->body()]);

                return response()->json([
                    'success' => false,
                    'message' => 'Layanan AI sedang tidak tersedia. Silakan input manual.',
                ], 502);
            }

            $result = $response->json();
            $jsonString = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

            $jsonString = preg_replace('/^```json\s*(.*?)\s*```$/is', '$1', trim($jsonString));

            // Parsing JSON dari Gemini
            $extractedData = json_decode($jsonString, true);

            if (! is_array($extractedData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI tidak dapat membaca faktur. Silakan input manual.',
                ], 422);
            }

            $namaToko = trim((string) ($extractedData['Nama Toko'] ?? $extractedData['nama_toko'] ?? ''));
            $nomorFaktur = trim((string) ($extractedData['Nomor Faktur'] ?? $extractedData['nomor_faktur'] ?? ''));
            $rawTotal = (string) ($extractedData['Total Tagihan'] ?? $extractedData['total_tagihan'] ?? '0');

            if ($nomorFaktur === '') {
                $nomorFaktur = 'INV-'.random_int(10000, 99999);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'nama_toko' => Str::limit($namaToko, 255, ''),
                    'nomor_faktur' => Str::limit($nomorFaktur, 100, ''),
                    'total_tagihan' => preg_replace('/[^0-9]/', '', $rawTotal) ?: '0',
                ],
            ]);

        } catch (ConnectionException $e) {
            Log::warning('Tidak bisa terhubung ke Gemini OCR.', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Koneksi ke layanan AI timeout. Silakan coba lagi atau input manual.',
            ], 504);
        } catch (\Throwable $e) {
            Log::error('OCR error tidak terduga.', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses faktur.',
            ], 500);
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
        $tokos = Toko::ownedByAdmin($sales->parent_admin_id)
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
            'foto_file' => [
                'nullable',
                'file',
                'max:10240',
                function (string $attribute, $value, \Closure $fail) {
                    if (! str_starts_with((string) $value->getMimeType(), 'image/')) {
                        $fail('File bukti faktur harus berupa gambar.');
                    }
                },
            ],
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

        try {
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
                'foto_path' => $fileName,
            ]);
        } catch (\Throwable $e) {
            // Jangan tinggalkan file yatim di storage kalau penyimpanan DB gagal.
            Storage::disk('supabase')->delete($fileName);
            Log::error('Gagal menyimpan faktur setelah upload foto.', [
                'sales_id' => $sales->id,
                'foto_path' => $fileName,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data faktur. Silakan coba lagi.',
            ], 500);
        }

        // Notifikasi untuk admin pemilik toko/sales tersebut.
        $admin = $sales->parentAdmin;
        if ($admin) {
            $toko = Toko::find($faktur->toko_id);

            try {
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
            } catch (\Throwable $e) {
                // Kegagalan notifikasi tidak boleh membatalkan faktur yang sudah tersimpan.
                Log::error('Gagal mengirim notifikasi faktur baru.', [
                    'faktur_id' => $faktur->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data faktur berhasil disimpan!',
            'foto_url' => $fotoUrl,
        ]);
    }
}
