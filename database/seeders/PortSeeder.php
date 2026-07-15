<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Port; 

class PortSeeder extends Seeder
{
    public function run(): void
    {
        $ports = [
            ['name' => 'Port of Hamburg', 'country_id' => 1, 'latitude' => 53.5511, 'longitude' => 9.9937],
            ['name' => 'Shanghai Port', 'country_id' => 2, 'latitude' => 31.2304, 'longitude' => 121.4737],
            ['name' => 'Tanjung Priok', 'country_id' => 3, 'latitude' => -6.1104, 'longitude' => 106.8744],
            ['name' => 'Port of Melbourne', 'country_id' => 4, 'latitude' => -37.8304, 'longitude' => 144.9128],
        ];

        foreach ($ports as $port) {
            Port::create($port);
        }
    }
}