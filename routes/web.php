<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\PortController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// ==========================================================
// 1. ROOT & PENGATURAN REDIRECT
// ==========================================================
Route::get('/', function () {
    if (Auth::check()) {
        // Jika Admin, lempar ke Admin Panel
        if (Auth::user()->role === 'admin') {
            return redirect('/admin');
        }
        // Jika User biasa, lempar ke Dashboard Utama
        return redirect('/countries');
    }
    // Jika belum login, paksa ke halaman login
    return redirect('/login');
});

// ==========================================================
// 2. RUTE AUTENTIKASI (LOGIN & REGISTER)
// ==========================================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================================
// 3. RUTE ADMIN PANEL (Hanya bisa diakses role 'admin')
// ==========================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Manajemen User
    Route::get('/users', [AdminController::class, 'usersIndex'])->name('users.index');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Manajemen Dataset Pelabuhan
    Route::get('/ports', [AdminController::class, 'portsIndex'])->name('ports.index');
    Route::post('/ports', [AdminController::class, 'storePort'])->name('ports.store');
    Route::put('/ports/{port}', [AdminController::class, 'updatePort'])->name('ports.update');
    Route::delete('/ports/{port}', [AdminController::class, 'destroyPort'])->name('ports.destroy');

    // Manajemen Artikel Analisis
    Route::get('/articles', [AdminController::class, 'articlesIndex'])->name('articles.index');
    Route::post('/articles', [AdminController::class, 'storeArticle'])->name('articles.store');
    Route::put('/articles/{article}', [AdminController::class, 'updateArticle'])->name('articles.update');
    Route::delete('/articles/{article}', [AdminController::class, 'destroyArticle'])->name('articles.destroy');
});

// ==========================================================
// 4. RUTE USER PORTAL (Halaman Utama Logistik)
// ==========================================================
// Semua halaman ini dibungkus middleware 'auth' agar tamu tidak bisa mengintip data
Route::middleware(['auth'])->group(function () {
    Route::get('/countries', function () { return view('countries'); })->name('countries');
    Route::get('/risk-score', function () { return view('risk-score'); });
    Route::get('/weather', function () { return view('weather'); });
    Route::get('/currency', function () { return view('currency'); });
    Route::get('/news', function () { return view('news'); })->name('news');
    Route::get('/port', [PortController::class, 'index'])->name('port');
    Route::get('/watchlist', function () { return view('watchlist'); });
    Route::get('/comparison', function () {
        $countries = DB::table('countries')->get();
        return view('comparison', ['countries' => $countries]);
    });
});

// ==========================================================
// 5. RUTE API (Untuk Ajax Javascript Frontend)
// ==========================================================
Route::prefix('api')->group(function () {
    
    // API Data Negara
    Route::get('/countries-data', function () {
        $countries = DB::table('countries')->orderBy('name', 'asc')->get();
        return response()->json($countries);
    });

    // API Kamus Sentimen Berita
    Route::get('/sentiment-words', function () {
        return response()->json([
            'positive' => DB::table('positive_words')->pluck('word'),
            'negative' => DB::table('negative_words')->pluck('word')
        ]);
    });

    // API Watchlist Group
    Route::prefix('watchlist')->group(function () {
        
        // Ambil Data Watchlist
        Route::get('/', function () {
            $userId = Auth::id() ?? 1;
            $watchlists = DB::table('watchlists')
                ->join('countries', 'watchlists.country_id', '=', 'countries.id')
                ->where('watchlists.user_id', $userId)
                ->pluck('countries.code');
            return response()->json($watchlists);
        });

        // Tambah Watchlist
        Route::post('/add', function (Request $request) {
            try {
                $userId = Auth::id() ?? 1;
                $country = DB::table('countries')->where('code', $request->code)->first();
                
                if (!$country) {
                    return response()->json(['success' => false, 'error' => 'Negara tidak ditemukan'], 404);
                }

                $exists = DB::table('watchlists')->where('user_id', $userId)->where('country_id', $country->id)->exists();
                if (!$exists) {
                    DB::table('watchlists')->insert([
                        'user_id' => $userId,
                        'country_id' => $country->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        });

        // Hapus Watchlist
        Route::post('/remove', function (Request $request) {
            try {
                $userId = Auth::id() ?? 1;
                $country = DB::table('countries')->where('code', $request->code)->first();

                if ($country) {
                    DB::table('watchlists')->where('user_id', $userId)->where('country_id', $country->id)->delete();
                }
                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        });
    });
});