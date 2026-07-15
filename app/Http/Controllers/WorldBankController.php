<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WorldBankController extends Controller
{
    public function getEconomyData(Request $request)
    {
        // Ambil kode negara dari URL, default ke 'DE' (Jerman) jika kosong
        $countryCode = $request->input('country', 'DE');

        // Tembak World Bank API khusus untuk indikator GDP
        $response = Http::get("https://api.worldbank.org/v2/country/{$countryCode}/indicator/NY.GDP.MKTP.CD", [
            'format' => 'json',
            'date' => '2023' // Mengambil data tahun yang sudah tercatat utuh
        ]);

        if ($response->successful() && isset($response->json()[1])) {
            $data = $response->json()[1][0]; // Ambil isi array pertama dari respon API

            return response()->json([
                'status' => 'success',
                'source' => 'World Bank API',
                'data' => [
                    'negara' => $data['country']['value'],
                    'gdp' => $data['value'] ?? 'Data tidak tersedia',
                    'tahun' => $data['date']
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengambil data ekonomi dari World Bank'
        ], 500);
    }
}