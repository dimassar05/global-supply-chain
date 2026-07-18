<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Port;
use App\Models\Country;

class PortSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Menghapus data pelabuhan lama yang salah...');
        
        // Disable foreign key sementara untuk membersihkan tabel
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Port::truncate(); 
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Mendownload dataset pelabuhan (Versi Lengkap dengan Negara)...');
        
        // Menggunakan dataset JSON baru yang memiliki data negara 100% akurat
        $url = 'https://raw.githubusercontent.com/marchah/sea-ports/master/lib/ports.json';
        
        $response = Http::withoutVerifying()->get($url);

        if ($response->successful()) {
            $portsData = $response->json();
            $count = 0;

            foreach ($portsData as $port) {
                $portName = $port['name'] ?? null;
                $countryName = $port['country'] ?? 'Unknown Country';
                
                // Koordinat format API ini: [longitude, latitude]
                $longitude = $port['coordinates'][0] ?? null;
                $latitude = $port['coordinates'][1] ?? null;

                if (empty($portName) || $longitude === null || $latitude === null) {
                    continue; 
                }

                $country = Country::firstOrCreate(['name' => $countryName]);

                Port::create([
                    'name' => $portName,
                    'country_id' => $country->id,
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ]);
                
                $count++;
            }
            $this->command->info("WOW! Sukses menyimpan {$count} Pelabuhan lengkap dengan nama Negaranya!");
        } else {
            $this->command->error("Gagal mendownload data dari URL.");
        }
    }
}