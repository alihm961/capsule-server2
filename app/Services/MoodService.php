<?php

namespace App\Services;

use App\Models\Mood;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class MoodService
{
    use ApiResponse;

    public function getAllMoods(Request $request)
    {
        $moods = Mood::all();
        return $this->responseJSON($moods, 'Moods retrieved successfully');
    }
}