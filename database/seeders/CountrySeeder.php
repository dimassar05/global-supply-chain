<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use Illuminate\Support\Facades\Http; // Wajib dipanggil untuk akses URL

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        // Memberikan pesan di terminal agar prosesnya kelihatan
        $this->command->info('Mengunduh data 250 negara dari GitHub mledoze...');

        // Mengambil data dari link yang kamu temukan
        $response = Http::withoutVerifying()
            ->get('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');

        if ($response->successful()) {
            $countries = $response->json();
            
            $this->command->info('Download sukses! Mulai memasukkan ke database...');

            foreach ($countries as $country) {
                // Ekstrak kode mata uang (karena di JSON bentuknya objek bersarang)
                $currencyCode = null;
                if (isset($country['currencies']) && !empty($country['currencies'])) {
                    // Ambil kunci pertama dari objek currencies (misal "IDR", "USD")
                    $currencyCode = array_key_first($country['currencies']); 
                }

                // Masukkan ke database kita
                Country::updateOrCreate(
                    ['name' => $country['name']['common']], // Nama umum negara
                    [
                        'code' => $country['cca2'] ?? null, // Kode 2 huruf (misal: ID, DE, US)
                        'region' => $country['region'] ?? 'Unknown',
                        'currency' => $currencyCode
                    ]
                );
            }

            $this->command->info('Selesai! Seluruh negara di dunia sudah masuk ke databasemu.');
        } else {
            $this->command->error('Gagal mengunduh data dari GitHub.');
        }
    }
}