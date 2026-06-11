<?php

namespace App\Http\Controllers\sales;

use App\Http\Controllers\Controller;
use App\Models\Faktur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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

            $apiKey = env('GEMINI_API_KEY');
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
                                    'data' => $base64Image
                                ]
                            ]
                        ]
                    ]
                ],
                // Respon Balik JSON
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                ]
            ]);

            if (!$response->successful()) {
                return response()->json(['success' => false, 'message' => 'API Error: ' . $response->body()], 500);
            }

            $result = $response->json();
            $jsonString = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
            
            // Parsing JSON dari Gemini
            $extractedData = json_decode($jsonString, true);

            return response()->json([
                'success' => true,
                'data' => [
                    'nama_toko' => $extractedData['Nama Toko'] ?? $extractedData['nama_toko'] ?? '',
                    'nomor_faktur' => $extractedData['Nomor Faktur'] ?? $extractedData['nomor_faktur'] ?? 'INV-'.rand(10000,99999),
                    'tanggal_nota' => $extractedData['Tanggal Nota'] ?? $extractedData['tanggal_nota'] ?? '',
                    'total_tagihan' => preg_replace('/[^0-9]/', '', $extractedData['Total Tagihan'] ?? '0'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function createFaktur(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'nomor_faktur' => 'required|string|max:100',
            'tanggal_nota' => 'required|string|max:100',
            'total_tagihan' => 'required|numeric|min:0',
            'total_dibayar' => 'nullable|numeric|min:0',
            'metode_bayar' => 'required|in:Cash,Transfer',
        ]);

        // Konversi tipe data agar operasi matematika presisi
        $totalTagihan = (int) $request->total_tagihan;
        $totalDibayar = (int) ($request->total_dibayar ?? 0);

        $faktur = Faktur::create([
            'nama_toko' => $request->nama_toko,
            'nomor_faktur' => $request->nomor_faktur,
            'tanggal_nota' => $request->tanggal_nota,
            'total_tagihan' => $totalTagihan,
            'total_dibayar' => $totalDibayar,
            'metode_bayar' => $request->metode_bayar,
            'status' => ($totalDibayar >= $totalTagihan) ? 'lunas' : 'belum_lunas',
            'sales_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Data faktur berhasil disimpan!'
        ]);
    }

    // Fungsi untuk menampilkan Dashboard Admin
    public function dashboard()
    {
        // Mengambil semua data faktur, diurutkan dari yang terbaru
        $fakturs = Faktur::latest()->get();
        return view('admin.dashboard', compact('fakturs'));
    }
}
