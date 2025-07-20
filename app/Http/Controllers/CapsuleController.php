<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CapsuleService;
use App\Traits\ApiResponse;

class CapsuleController extends Controller {


    use ApiResponse;

     function store(Request $request, CapsuleService $service) {
        return $service->create($request);
    }

     function index(Request $request, CapsuleService $service) {
        return $service->getUserCapsules($request);
    }

     function publicWall(Request $request, CapsuleService $service) {
        return $service->getPublicCapsules($request);
    }

     function destroy($id, CapsuleService $service) {
        return $service->delete($id);
    }

    function getAllCountries(CapsuleService $service) {
        return $service->getAllCountries();
    }


}