<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country; 

class CountryController extends Controller
{
    public function getCountryInfo(Request $request)
    {
        $countryName = $request->input('name', 'Germany'); 

        // Tarik data langsung dari database
        $country = Country::where('name', $countryName)->first();

        if ($country) {
            return response()->json([
                'status' => 'success',
                'source' => 'Database Internal (Seeder)',
                'data' => [
                    'negara' => $country->name,
                    'kode' => $country->code,
                    'wilayah' => $country->region,
                    'mata_uang' => $country->currency
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => "Data negara {$countryName} tidak ditemukan."
        ], 404);
    }
}