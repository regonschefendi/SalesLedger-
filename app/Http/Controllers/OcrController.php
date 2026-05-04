<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OcrController extends Controller
{
    public function index()
    {
        // Menampilkan halaman utama
        return view('ocr');
    }

    // public function cekModel()
    // {
    //     $apiKey = env('GEMINI_API_KEY');
    //     $url = "https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}";
        
    //     $response = Http::get($url);
        
    //     return response()->json($response->json());
    // }

    public function processApi(Request $request)
    {
        // 1. Validasi File (Bisa dari kamera atau galeri)
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            $imagePath = $request->file('image')->getPathname();
            $mimeType = $request->file('image')->getMimeType();
            $base64Image = base64_encode(file_get_contents($imagePath));

            $apiKey = env('GEMINI_API_KEY');
            // Pastikan pakai model yang tersedia di API Key lu (contoh pake 1.5-flash-latest atau 001)
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

            // 2. Prompting Super Spesifik buat Ekstrak 3 Data Utama
            $prompt = 'Lu adalah asisten admin data entry. Ekstrak data dari faktur ini. Cari "Nama Toko" (biasanya di samping/bawah tulisan Kepada/Penerima/Tgl). Cari "Tanggal Nota" (Tanggal faktur). Cari "Total Tagihan" (Jumlah total akhir berupa angka murni tanpa titik/koma/Rp). Kembalikan HANYA JSON.';

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
                // 3. SECURITY & CLEAN CODE: Paksa Gemini ngebalikin JSON murni!
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
                    'tanggal_nota' => $extractedData['Tanggal Nota'] ?? $extractedData['tanggal_nota'] ?? '',
                    'total_tagihan' => $extractedData['Total Tagihan'] ?? $extractedData['total_tagihan'] ?? '',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
