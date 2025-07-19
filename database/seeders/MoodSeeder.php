<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mood;

class MoodSeeder extends Seeder
{
    public function run(): void
    {
        $moods = ['happy', 'sad', 'excited', 'nostalgic', 'grateful', 'anxious', 'peaceful', 'hopeful'];
        foreach ($moods as $mood) {
            Mood::create(['name' => $mood]);
        }
    }
}
