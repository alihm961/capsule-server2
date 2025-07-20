<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CountryService;
use App\Traits\ApiResponse;

class CountryController extends Controller {

    use ApiResponse;

    function index(Request $request, CountryService $service) {

        return $service->getAllCountries($request);
    }
}