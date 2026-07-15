<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PositiveWord;
use App\Models\NegativeWord;

class WordSeeder extends Seeder
{
    public function run(): void
    {
        $positiveWords = ['growth', 'increase', 'profit', 'stable', 'improve'];
        foreach ($positiveWords as $word) {
            PositiveWord::create(['word' => $word]);
        }

        $negativeWords = ['war', 'crisis', 'inflation', 'delay', 'disaster'];
        foreach ($negativeWords as $word) {
            NegativeWord::create(['word' => $word]);
        }
    }
}