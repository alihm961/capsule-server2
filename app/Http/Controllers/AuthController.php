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
        $user = AuthService::login($request);
        if ($user) {
            return $this->responseJSON($user, 'Login successful');
        }
        return $this->responseJSON(null, 'Invalid credentials', 401);
    }

    public function register(Request $request)
    {
        $user = AuthService::register($request);
        return $this->responseJSON($user, 'Registration successful', true);
    }
}