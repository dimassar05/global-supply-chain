<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

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