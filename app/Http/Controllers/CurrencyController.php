<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    public function getExchangeRate(Request $request)
    {
        // Default base currency USD to EUR
        $base = $request->input('base', 'USD');
        $target = $request->input('target', 'EUR');

        // Kita gunakan API publik (tanpa key) yang umum dipakai untuk testing/proyek
        $response = Http::withoutVerifying()
            ->get("https://api.exchangerate-api.com/v4/latest/{$base}");

        if ($response->successful()) {
            $data = $response->json();
            $rate = $data['rates'][$target] ?? 'Tidak tersedia';

            return response()->json([
                'status' => 'success',
                'source' => 'ExchangeRate API',
                'data' => [
                    'base' => $base,
                    'target' => $target,
                    'rate' => $rate,
                    'last_update' => $data['date']
                ]
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'Gagal mengambil kurs'], 500);
    }
}