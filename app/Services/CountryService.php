<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class CountryService {

    use ApiResponse;
    

    function getAllCountries($request) {

        $countries = Country::all();
        return $this->responseJSON($countries, 'Countries retrieved successfully');
    }
}