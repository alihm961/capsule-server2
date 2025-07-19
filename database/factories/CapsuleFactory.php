<?php

use App\Models\User;
use App\Models\Mood;
use App\Models\Country;

function definition(): array
{
    return [
        'user_id'    => User::inRandomOrder()->first()->id,
        'title'      => $this->faker->sentence(),
        'message'    => $this->faker->paragraph(),
        'image_path' => null,
        'audio_path' => null,
        'ip_address' => $this->faker->ipv4(),
        'location'   => $this->faker->address(),
        'mood_id'    => Mood::inRandomOrder()->first()->id,
        'country_id' => Country::inRandomOrder()->first()->id,
        'is_public'  => $this->faker->boolean(),
        'is_surprise' => $this->faker->boolean(),
        'reveal_at'  => now()->addDays(rand(1, 30)),
    ];
}
