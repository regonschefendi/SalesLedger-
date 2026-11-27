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
            $prompt = 'Anda adalah asisten admin entri data. Ekstrak data dari foto faktur/nota ini. '
                .'Kembalikan HANYA JSON valid tanpa markdown dengan format: '
                .'{"nama_toko":"...","nomor_faktur":"...","tanggal_nota":"YYYY-MM-DD","total_tagihan":0}. '
                .'Nama toko biasanya berada di samping/bawah tulisan Kepada/Penerima/Tgl. '
                .'total_tagihan adalah jumlah akhir berupa angka murni tanpa titik, koma, atau Rp. '
                .'Jika suatu data tidak ditemukan, kosongkan string atau isi 0.';

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
                    'message' => 'Layanan AI sedang tidak tersedia. Silakan masukkan secara manual.',
                ], 502);
            }

            $result = $response->json();
            $jsonString = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

            // Parsing JSON dari Gemini. Model kadang membungkus JSON dengan
            // markdown atau menambahkan kalimat lain, jadi ambil payload JSON
            // yang benar-benar valid.
            $extractedData = $this->extractJsonPayload((string) $jsonString);

            if (! is_array($extractedData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI tidak dapat membaca faktur. Silakan masukkan secara manual.',
                ], 422);
            }

            $namaToko = trim((string) ($extractedData['Nama Toko'] ?? $extractedData['nama_toko'] ?? ''));
            $nomorFaktur = trim((string) ($extractedData['Nomor Faktur'] ?? $extractedData['nomor_faktur'] ?? ''));
            $rawTanggal = (string) ($extractedData['Tanggal Nota'] ?? $extractedData['tanggal_nota'] ?? $extractedData['Tanggal Faktur'] ?? $extractedData['tanggal_faktur'] ?? '');
            $rawTotal = (string) ($extractedData['Total Tagihan'] ?? $extractedData['total_tagihan'] ?? '0');

            if ($nomorFaktur === '') {
                $nomorFaktur = 'INV-'.random_int(10000, 99999);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'nama_toko' => Str::limit($namaToko, 255, ''),
                    'nomor_faktur' => Str::limit($nomorFaktur, 100, ''),
                    'tanggal_nota' => $this->normalizeOcrDate($rawTanggal),
                    'total_tagihan' => $this->normalizeOcrTotal($rawTotal),
                ],
            ]);

        } catch (ConnectionException $e) {
            Log::warning('Tidak bisa terhubung ke Gemini OCR.', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Koneksi ke layanan AI melewati batas waktu. Silakan coba lagi atau masukkan secara manual.',
            ], 504);
        } catch (\Throwable $e) {
            Log::error('OCR error tidak terduga.', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses faktur.',
            ], 500);
        }
    }

    /**
     * Ambil objek JSON dari teks yang dikembalikan model AI. Model kadang
     * menambahkan markdown atau kalimat pembuka/penutup.
     */
    private function extractJsonPayload(string $text): ?array
    {
        $text = trim($text);

        if ($text === '') {
            return null;
        }

        // Buang pembungkus markdown ```json ... ```.
        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $text);
        $cleaned = preg_replace('/\s*```$/', '', (string) $cleaned);
        $cleaned = trim((string) $cleaned);

        $decoded = json_decode($cleaned, true);

        if (is_array($decoded)) {
            return $decoded;
        }

        // Fallback: ambil dari kurung kurawal pertama sampai terakhir.
        $start = strpos($text, '{');
        $end = strrpos($text, '}');

        if ($start !== false && $end !== false && $end > $start) {
            $decoded = json_decode(substr($text, $start, $end - $start + 1), true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Ubah tanggal hasil OCR menjadi format dd/mm/yyyy yang dipakai form.
     */
    private function normalizeOcrDate(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        // Buang bagian waktu, mis. "2026-11-24T10:30:00" atau "24/11/2026 10:30".
        $value = trim((string) preg_replace('/[T\s]\d{1,2}:\d{2}(?::\d{2})?.*$/', '', $value));

        // Format ISO: 2026-11-24.
        if (preg_match('/^(\d{4})[\s\/\-.](\d{1,2})[\s\/\-.](\d{1,2})$/', $value, $matches)) {
            $year = (int) $matches[1];
            $month = (int) $matches[2];
            $day = (int) $matches[3];

            if (checkdate($month, $day, $year)) {
                return sprintf('%02d/%02d/%04d', $day, $month, $year);
            }
        }

        // Format angka: 24/11/2026, 24-11-2026, 24.11.2026, atau 11/24/2026.
        if (preg_match('/^(\d{1,2})[\s\/\-.](\d{1,2})[\s\/\-.](\d{2,4})$/', $value, $matches)) {
            $day = (int) $matches[1];
            $month = (int) $matches[2];
            $year = $this->normalizeOcrYear((int) $matches[3]);

            // Sebagian model AI mengembalikan format bulan/tanggal/tahun.
            if (! checkdate($month, $day, $year) && checkdate($day, $month, $year)) {
                [$day, $month] = [$month, $day];
            }

            if (checkdate($month, $day, $year)) {
                return sprintf('%02d/%02d/%04d', $day, $month, $year);
            }
        }

        // Format dengan nama bulan di belakang: 24 November 2026 / 24 Nov 2026.
        if (preg_match('/^(\d{1,2})[\s\-\/\.]+([A-Za-z]+)[\s\-\/\.]+(\d{2,4})$/', $value, $matches)) {
            $day = (int) $matches[1];
            $month = $this->ocrMonthNumber($matches[2]);
            $year = $this->normalizeOcrYear((int) $matches[3]);

            if ($month > 0 && checkdate($month, $day, $year)) {
                return sprintf('%02d/%02d/%04d', $day, $month, $year);
            }
        }

        // Format dengan nama bulan di depan: November 24, 2026 / Nov 24 2026.
        if (preg_match('/^([A-Za-z]+)[\s\-\/\.]+(\d{1,2}),?[\s\-\/\.]+(\d{2,4})$/', $value, $matches)) {
            $month = $this->ocrMonthNumber($matches[1]);
            $day = (int) $matches[2];
            $year = $this->normalizeOcrYear((int) $matches[3]);

            if ($month > 0 && checkdate($month, $day, $year)) {
                return sprintf('%02d/%02d/%04d', $day, $month, $year);
            }
        }

        return '';
    }

    private function normalizeOcrYear(int $year): int
    {
        return $year < 100 ? $year + 2000 : $year;
    }

    /**
     * Ubah nominal hasil OCR menjadi angka murni. Menangani format ribuan
     * Indonesia (1.500.000) maupun internasional (1,500,000.00).
     */
    private function normalizeOcrTotal($value): string
    {
        $raw = trim((string) $value);

        if ($raw === '') {
            return '0';
        }

        // Angka negatif tidak masuk akal untuk total tagihan.
        if (str_starts_with($raw, '-') || preg_match('/^\(.*\)$/', $raw)) {
            return '0';
        }

        $numeric = preg_replace('/[^0-9,.]/', '', $raw);

        if ($numeric === null || $numeric === '') {
            return '0';
        }

        $lastComma = strrpos($numeric, ',');
        $lastDot = strrpos($numeric, '.');
        $decimalPosition = -1;

        if ($lastComma !== false && $lastDot !== false) {
            // Pemisah yang paling kanan adalah pemisah desimal.
            $decimalPosition = max($lastComma, $lastDot);
        } elseif ($lastComma !== false) {
            // Satu koma dengan 3 digit di belakang = pemisah ribuan.
            $digitsAfter = strlen($numeric) - $lastComma - 1;
            $decimalPosition = ($digitsAfter !== 3) ? $lastComma : -1;
        } elseif ($lastDot !== false) {
            // Satu titik dengan 3 digit di belakang = pemisah ribuan.
            $digitsAfter = strlen($numeric) - $lastDot - 1;
            $decimalPosition = ($digitsAfter !== 3) ? $lastDot : -1;
        }

        if ($decimalPosition >= 0) {
            $numeric = substr($numeric, 0, $decimalPosition);
        }

        $digits = preg_replace('/[^0-9]/', '', $numeric);

        if ($digits === null || $digits === '') {
            return '0';
        }

        $normalized = ltrim($digits, '0');

        return $normalized === '' ? '0' : $normalized;
    }

    private function ocrMonthNumber(string $name): int
    {
        $months = [
            'januari' => 1, 'jan' => 1, 'january' => 1,
            'februari' => 2, 'feb' => 2, 'february' => 2,
            'maret' => 3, 'mar' => 3, 'march' => 3,
            'april' => 4, 'apr' => 4,
            'mei' => 5, 'may' => 5,
            'juni' => 6, 'jun' => 6, 'june' => 6,
            'juli' => 7, 'jul' => 7, 'july' => 7,
            'agustus' => 8, 'agu' => 8, 'ags' => 8, 'aug' => 8, 'august' => 8,
            'september' => 9, 'sep' => 9, 'sept' => 9,
            'oktober' => 10, 'okt' => 10, 'oct' => 10, 'october' => 10,
            'november' => 11, 'nov' => 11,
            'desember' => 12, 'des' => 12, 'dec' => 12, 'december' => 12,
        ];

        return $months[mb_strtolower(trim($name))] ?? 0;
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
            'tanggal_nota' => [
                'required',
                'string',
                'max:100',
                function (string $attribute, $value, \Closure $fail) {
                    if (! preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', (string) $value, $matches)) {
                        $fail('Format tanggal faktur harus dd/mm/yyyy.');

                        return;
                    }

                    if (! checkdate((int) $matches[2], (int) $matches[1], (int) $matches[3])) {
                        $fail('Tanggal faktur tidak valid.');
                    }
                },
            ],
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
            'toko_id.exists' => 'Toko yang dipilih bukan milik admin Anda.',
        ]);

        $request['tanggal_nota'] = Carbon::createFromFormat('d/m/Y', $request->tanggal_nota)->format('Y-m-d');

        if (! $request->hasFile('foto_file')) {
            return response()->json([
                'success' => false,
                'message' => 'GAGAL: Server tidak menerima berkas foto. Hal ini biasanya karena ukuran berkas melebihi batas server (upload_max_filesize) atau proses kompresi gagal.',
            ]);
        }

        $file = $request->file('foto_file');

        // Cek kalau filenya corrupt saat diupload
        if (! $file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'GAGAL: Berkas rusak saat diunggah. Kode galat: '.$file->getErrorMessage(),
            ]);
        }

        // PROSES UPLOAD KE SUPABASE
        try {
            $fileName = time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();

            $uploadSukses = Storage::disk('supabase')->putFileAs('/', $file, $fileName, 'public');

            if ($uploadSukses) {
                $fotoUrl = config('services.supabase.url').'/storage/v1/object/public/'.config('services.supabase.bucket').'/'.$fileName;
            } else {
                return response()->json(['success' => false, 'message' => 'Gagal mengunggah berkas ke Supabase. Periksa Access Key di .env!']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Galat Server Cloud: '.$e->getMessage()]);
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
                        $sales->full_name.' memasukkan nota '.$faktur->nomor_faktur
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
            'faktur_id' => $faktur->id,
            'invoice_url' => route('sales.riwayat.invoice', $faktur->id),
            'invoice_download_url' => route('sales.riwayat.invoice.download', $faktur->id),
        ]);
    }
}
