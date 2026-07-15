<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\WorldBankController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AnalyticController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Endpoint buatan untuk API Cuaca (Open-Meteo)
Route::get('/weather', [WeatherController::class, 'getForecast']);

// Endpoint buatan untuk World Bank API (Ekonomi)
Route::get('/economy', [WorldBankController::class, 'getEconomyData']);

// Endpoint buatan untuk REST Countries API
Route::get('/country-info', [CountryController::class, 'getCountryInfo']);

Route::get('/currency', [CurrencyController::class, 'getExchangeRate']);

Route::get('/ports', [PortController::class, 'index']);

Route::get('/news', [NewsController::class, 'getNews']);

Route::get('/analyze-news', [AnalyticController::class, 'analyzeNewsSentiment']);

Route::get('/risk-score', [AnalyticController::class, 'calculateGlobalRisk']);