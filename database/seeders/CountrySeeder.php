<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Germany', 'code' => 'DE', 'region' => 'Europe'],
            ['name' => 'China', 'code' => 'CN', 'region' => 'Asia'],
            ['name' => 'Indonesia', 'code' => 'ID', 'region' => 'Asia'],
            ['name' => 'Australia', 'code' => 'AU', 'region' => 'Oceania'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}