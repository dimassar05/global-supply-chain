<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function getForecast(Request $request)
    {
        // 1. Ambil koordinat dari request, atau gunakan default (contoh: Berlin, Germany)
        $lat = $request->input('latitude', 52.52);
        $lon = $request->input('longitude', 13.41);

        // 2. Tembak Open-Meteo API
        $response = Http::get("https://api.open-meteo.com/v1/forecast", [
            'latitude' => $lat,
            'longitude' => $lon,
            'current' => 'temperature_2m,rain,wind_speed_10m,weather_code',
            'timezone' => 'auto'
        ]);

        // 3. Jika API berhasil merespons
        if ($response->successful()) {
            $data = $response->json();
            $current = $data['current'];

            // Logika sederhana untuk Risiko Badai (Weather code 95-99 di Open-Meteo adalah badai petir)
            $stormRiskCode = $current['weather_code'] ?? 0;
            $stormRiskStatus = ($stormRiskCode >= 95) ? 'Tinggi (Ada Badai)' : 'Rendah (Aman)';

            // 4. Susun ulang data JSON agar sesuai dengan permintaan spesifikasi project
            return response()->json([
                'status' => 'success',
                'source' => 'Open-Meteo API',
                'data' => [
                    'temperatur' => $current['temperature_2m'] . ' °C',
                    'curah_hujan' => $current['rain'] . ' mm',
                    'kecepatan_angin' => $current['wind_speed_10m'] . ' km/h',
                    'risiko_badai' => $stormRiskStatus
                ]
            ]);
        }

        // 5. Jika gagal
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengambil data cuaca'
        ], 500);
    }
}