<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CapsuleService;
use App\Traits\ApiResponse;

class CapsuleController extends Controller
{
    use ApiResponse;

    public function store(Request $request, CapsuleService $service)
    {
        return $service->create($request);
    }

    public function index(Request $request, CapsuleService $service)
    {
        return $service->getUserCapsules($request);
    }

    public function publicWall(Request $request, CapsuleService $service)
    {
        return $service->getPublicCapsules($request);
    }

    public function destroy($id, CapsuleService $service)
    {
        return $service->delete($id);
    }
}