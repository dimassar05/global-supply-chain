<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\WorldBankController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\NewsController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Endpoint buatan mahasiswa untuk API Cuaca (Open-Meteo)
Route::get('/weather', [WeatherController::class, 'getForecast']);

// Endpoint buatan mahasiswa untuk World Bank API (Ekonomi)
Route::get('/economy', [WorldBankController::class, 'getEconomyData']);

// Endpoint buatan mahasiswa untuk REST Countries API
Route::get('/country-info', [CountryController::class, 'getCountryInfo']);

Route::get('/currency', [CurrencyController::class, 'getExchangeRate']);

Route::get('/ports', [PortController::class, 'index']);

Route::get('/news', [NewsController::class, 'getNews']);