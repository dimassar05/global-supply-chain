<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CountryController extends Controller
{
    public function getCountryInfo(Request $request)
    {
        $countryName = $request->input('name', 'Germany'); 

        // UPDATE: Mengubah versi API di URL dari v3.1 menjadi v5 sesuai instruksi error
        $response = Http::withoutVerifying()
            ->withHeaders([
                'User-Agent' => 'GlobalSupplyChainPlatform/1.0'
            ])
            ->get("https://restcountries.com/v5/name/{$countryName}");

        $responseData = $response->json();

        // Cek apakah sukses dan data ada
        if ($response->successful() && !empty($responseData) && isset($responseData[0])) {
            $data = $responseData[0]; 

            return response()->json([
                'status' => 'success',
                'source' => 'REST Countries API',
                'data' => [
                    // Menambahkan pengaman ganda (?? $data['name']) berjaga-jaga jika struktur JSON v5 sedikit berubah
                    'negara' => $data['name']['common'] ?? $data['name'] ?? 'Tidak diketahui',
                    'wilayah' => $data['region'] ?? 'Tidak diketahui',
                    'mata_uang' => collect($data['currencies'] ?? [])->keys()->first() ?? 'Tidak diketahui',
                    'bahasa' => collect($data['languages'] ?? [])->values()->implode(', ') ?? 'Tidak diketahui'
                ]
            ]);
        }

        // Jika masih gagal, kita keluarkan pesan error ASLI dari API servernya
        return response()->json([
            'status' => 'error',
            'message' => "Gagal mengambil data untuk negara: {$countryName}.",
            'debug_http_status' => $response->status(), 
            'debug_api_response' => $responseData 
        ], $response->status() === 0 ? 500 : $response->status());
    }
}