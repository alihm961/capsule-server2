<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = ['Netherlands', 'Lebanon', 'France', 'Germany', 'Italy', 'Spain'];
        foreach ($countries as $country) {
            Country::create(['name' => $country]);
        }
    }
}
