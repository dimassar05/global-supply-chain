<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // Wajib untuk tembak API Cuaca & Ekonomi
use App\Models\NewsCache;

class AnalyticController extends Controller
{
    // Fungsi 1: Analisis Sentimen (Yang tadi sudah jalan)
    public function analyzeNewsSentiment()
    {
        $positiveWords = DB::table('positive_words')->pluck('word')->toArray();
        $negativeWords = DB::table('negative_words')->pluck('word')->toArray();
        $newsList = NewsCache::all();

        if ($newsList->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Belum ada berita'], 404);
        }

        $results = [];
        foreach ($newsList as $news) {
            $textToAnalyze = strtolower($news->title); 
            $posCount = 0; $negCount = 0;

            foreach ($positiveWords as $word) { if (str_contains($textToAnalyze, strtolower($word))) $posCount++; }
            foreach ($negativeWords as $word) { if (str_contains($textToAnalyze, strtolower($word))) $negCount++; }

            $sentiment = 'Netral';
            if ($posCount > $negCount) $sentiment = 'Positif';
            elseif ($negCount > $posCount) $sentiment = 'Negatif';

            $results[] = ['id_berita' => $news->id, 'judul' => $news->title, 'skor_positif' => $posCount, 'skor_negatif' => $negCount, 'sentimen' => $sentiment];
        }

        return response()->json(['status' => 'success', 'data' => $results]);
    }

    // Fungsi 2: (BARU) Mesin Kalkulator Skor Risiko Logistik
    public function calculateGlobalRisk()
    {
        // --- 1. RISIKO BERITA (Maks 40 Poin) ---
        $negativeWords = DB::table('negative_words')->pluck('word')->toArray();
        $newsList = NewsCache::latest()->take(10)->get();
        $newsRiskScore = 0;
        $negCount = 0;

        foreach ($newsList as $news) {
            $text = strtolower($news->title);
            foreach ($negativeWords as $word) {
                if (str_contains($text, strtolower($word))) {
                    $negCount++;
                }
            }
        }

        if ($negCount >= 3) {
            $newsRiskScore = 40; 
        } elseif ($negCount > 0) {
            $newsRiskScore = 20; 
        }

        // --- 2. RISIKO CUACA (Maks 30 Poin) ---
        // Contoh mengecek cuaca di koordinat Jakarta (Tanjung Priok)
        $weatherRiskScore = 0;
        $weatherCondition = "Cerah/Aman";
        $weatherResponse = Http::withoutVerifying()->get('https://api.open-meteo.com/v1/forecast?latitude=-6.2088&longitude=106.8456&current_weather=true');

        if ($weatherResponse->successful()) {
            $windSpeed = $weatherResponse->json()['current_weather']['windspeed'];

            if ($windSpeed > 20) {
                $weatherRiskScore = 30; 
                $weatherCondition = "Angin Kencang ({$windSpeed} km/h)";
            } elseif ($windSpeed > 10) {
                $weatherRiskScore = 15;
                $weatherCondition = "Angin Sedang ({$windSpeed} km/h)";
            }
        }

        // --- 3. RISIKO EKONOMI (Maks 30 Poin) ---
        // Contoh mengecek nilai tukar USD ke IDR
        $economyRiskScore = 0;
        $economyCondition = "Stabil";
        $ecoResponse = Http::withoutVerifying()->get('https://api.exchangerate-api.com/v4/latest/USD');

        if ($ecoResponse->successful()) {
            $idrRate = $ecoResponse->json()['rates']['IDR'] ?? 15000;

            if ($idrRate > 16500) {
                $economyRiskScore = 30; // Rupiah anjlok, biaya logistik naik
                $economyCondition = "Rupiah Melemah (Rp {$idrRate})";
            } elseif ($idrRate > 15500) {
                $economyRiskScore = 15;
                $economyCondition = "Fluktuasi Ringan (Rp {$idrRate})";
            } else {
                $economyCondition = "Kurs Aman (Rp {$idrRate})";
            }
        }

        // --- 4. KALKULASI TOTAL SKOR ---
        $totalRiskScore = $newsRiskScore + $weatherRiskScore + $economyRiskScore;

        // --- 5. KESIMPULAN & REKOMENDASI AI ---
        if ($totalRiskScore >= 70) {
            $status = "BAHAYA (Kritis)";
            $rekomendasi = "Tunda pengiriman atau cari rute alternatif segera!";
        } elseif ($totalRiskScore >= 40) {
            $status = "WASPADA (Sedang)";
            $rekomendasi = "Pantau kondisi secara berkala, siapkan rencana cadangan.";
        } else {
            $status = "AMAN (Rendah)";
            $rekomendasi = "Lanjutkan pengiriman sesuai jadwal.";
        }

        return response()->json([
            'status' => 'success',
            'skor_total' => $totalRiskScore . ' / 100',
            'tingkat_risiko' => $status,
            'rekomendasi_ai' => $rekomendasi,
            'rincian' => [
                'berita' => ['skor' => $newsRiskScore, 'keterangan' => "Ditemukan {$negCount} kata kunci negatif di berita."],
                'cuaca' => ['skor' => $weatherRiskScore, 'keterangan' => $weatherCondition],
                'ekonomi' => ['skor' => $economyRiskScore, 'keterangan' => $economyCondition],
            ]
        ]);
    }
}