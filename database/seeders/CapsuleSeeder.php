<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Capsule;
use App\Models\User;
use App\Models\Mood;
use App\Models\Country;

class CapsuleSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); 
        $mood = Mood::first(); 
        $country = Country::first();

        Capsule::create([
            'user_id' => $user->id,
            'title' => 'Seeded Capsule Title',
            'message' => 'This is a test capsule created from seeder.',
            'image_path' => null,
            'audio_path' => null,
            'ip_address' => '127.0.0.1',
            'location' => 'Test Location',
            'mood_id' => $mood->id,
            'country_id' => $country->id,
            'is_public' => true,
            'is_surprise' => false,
            'reveal_at' => now()->addDays(7),
        ]);
    }
}