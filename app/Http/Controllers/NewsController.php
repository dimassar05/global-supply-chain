<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\NewsCache;

class NewsController extends Controller
{
    public function getNews(Request $request)
    {
        $topic = $request->input('topic', 'logistics');
        $apiKey = 'eb68ad8b81cd4badd54a7b8406a0a7b2'; 
        
        $response = Http::withoutVerifying()
            ->get("https://gnews.io/api/v4/search", [
                'q' => $topic,
                'token' => $apiKey,
                'lang' => 'en'
            ]);

        if ($response->successful()) {
            $articles = $response->json()['articles'];

            // Menyimpan ke cache
            foreach ($articles as $article) {
                NewsCache::updateOrCreate(
                    ['title' => $article['title']], // Cek apakah judul sudah ada
                    ['url' => $article['url'], 'country_id' => 1] 
                );
            }

            return response()->json([
                'status' => 'success',
                'data' => $articles
            ]);
        }

        return response()->json([
            'status' => 'error', 
            'message' => 'Gagal ambil berita: ' . $response->body()
        ], 500);
    }
}