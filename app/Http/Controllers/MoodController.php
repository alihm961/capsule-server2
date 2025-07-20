<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MoodService;
use App\Traits\ApiResponse;

class MoodController extends Controller
{
    use ApiResponse;

    public function index(Request $request, MoodService $service)
    {
        return $service->getAllMoods($request);
    }
}
