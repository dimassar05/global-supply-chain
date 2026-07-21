<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;         
use App\Http\Controllers\PortController;

// Redirect dari halaman utama ke halaman countries
Route::get('/', function () {
    return redirect('/countries');
});

// Rute halaman countries
Route::get('/countries', function () {
    return view('countries');
});

Route::get('/api/countries-data', function () {
    // Mengambil seluruh isi tabel 'countries'
    $countries = DB::table('countries')->orderBy('name', 'asc')->get();
    return response()->json($countries);
});

Route::get('/risk-score', function () {
    return view('risk-score');
});

Route::get('/weather', function () {
    return view('weather');
});

Route::get('/currency', function () {
    return view('currency');
});

Route::get('/news', function () {
    return view('news');
})->name('news');

Route::get('/port', [PortController::class, 'index'])->name('port');

Route::get('/comparison', function () {
    // 1. Ambil semua data dari tabel 'countries'
    $countries = DB::table('countries')->get(); 
    
    // 2. Kirim data tersebut ke view 'comparison'
    return view('comparison', [
        'countries' => $countries
    ]);
});

// Route untuk menampilkan halaman Watchlist
Route::get('/watchlist', function () {
    return view('watchlist');
});

// ==========================================================
// --- API UNTUK FITUR WATCHLIST (DATABASE) ---
// ==========================================================

// 1. Ambil data watchlist milik user
Route::get('/api/watchlist', function () {
    $userId = Auth::id() ?? 1; 
    
    // Kita lakukan JOIN agar yang dikirim ke Javascript adalah Kode Negara (Teks), bukan Angka ID
    $watchlists = DB::table('watchlists')
                    ->join('countries', 'watchlists.country_id', '=', 'countries.id')
                    ->where('watchlists.user_id', $userId)
                    ->pluck('countries.code'); // Ambil kolom 'code' (contoh: "ID", "US")
                    
    return response()->json($watchlists);
});

// 2. Simpan negara ke watchlist
Route::post('/api/watchlist/add', function (Request $request) {
    try {
        $userId = Auth::id() ?? 1;
        $countryCode = $request->code; // Frontend mengirim kode huruf, misal: "ID"

        // Terjemahkan kode huruf menjadi angka ID
        $country = DB::table('countries')->where('code', $countryCode)->first();
        
        if (!$country) {
            return response()->json(['success' => false, 'error' => 'Negara tidak ditemukan di database.'], 404);
        }

        // Cek agar tidak duplikat di database
        $exists = DB::table('watchlists')
                    ->where('user_id', $userId)
                    ->where('country_id', $country->id) // Gunakan angka ID dari tabel countries
                    ->exists();

        if (!$exists) {
            DB::table('watchlists')->insert([
                'user_id' => $userId,
                'country_id' => $country->id, // Simpan angka ID
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        return response()->json(['success' => true]);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});

// 3. Hapus negara dari watchlist
Route::post('/api/watchlist/remove', function (Request $request) {
    try {
        $userId = Auth::id() ?? 1;
        $countryCode = $request->code;

        // Cari angka ID negaranya dulu
        $country = DB::table('countries')->where('code', $countryCode)->first();

        if ($country) {
            DB::table('watchlists')
                ->where('user_id', $userId)
                ->where('country_id', $country->id) // Hapus berdasarkan angka ID
                ->delete();
        }

        return response()->json(['success' => true]);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
});

// ==========================================================
// --- API UNTUK KAMUS SENTIMEN BERITA (NEWS INTELLIGENCE) ---
// ==========================================================
Route::get('/api/sentiment-words', function () {
    // Ambil semua kata dari tabel
    $positiveWords = DB::table('positive_words')->pluck('word');
    $negativeWords = DB::table('negative_words')->pluck('word');
    
    // Kirim ke frontend (halaman news) dalam bentuk JSON
    return response()->json([
        'positive' => $positiveWords,
        'negative' => $negativeWords
    ]);
});