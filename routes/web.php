<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
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
    // 1. Ambil semua data dari tabel 'countries' (sesuaikan jika nama tabelmu berbeda)
    $countries = DB::table('countries')->get(); 
    
    // 2. Kirim data tersebut ke view 'comparison'
    return view('comparison', [
        'countries' => $countries
    ]);
});

Route::get('/watchlist', function () {
    return view('watchlist');
});