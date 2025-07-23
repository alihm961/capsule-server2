<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(Request $request)
    {
        $data = AuthService::login($request);
        if (!$data) {
            return $this->responseJSON(null, 'Invalid credentials', 401);
        }
        return $this->responseJSON($data, 'Login successful', 200);
    }

    public function register(Request $request)
    {
        $user = AuthService::register($request);
        return $this->responseJSON($user, 'Registration successful', 200);
    }
}

/*Testing as always */