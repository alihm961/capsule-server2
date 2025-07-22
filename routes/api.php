<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CapsuleController;
use App\Http\Controllers\MoodController;
use App\Http\Controllers\CountryController;

Route::group(["prefix" => "v1"], function () {

    Route::group(["prefix" => "guest"], function () {
        Route::post("/login", [AuthController::class, 'login']);
        Route::post("/register", [AuthController::class, 'register']);
        Route::get('/moods', [MoodController::class, 'index']);
    });

    Route::group(["middleware" => "auth:api"], function () {

        Route::group(["prefix" => "capsules"], function () {
            Route::get('countries', [CountryController::class, 'index']);
            Route::get("/user", [CapsuleController::class, 'index']); 
            Route::post("/create", [CapsuleController::class, 'store']); 
            Route::get("/public", [CapsuleController::class, 'publicWall']); 
            Route::delete("/{id}", [CapsuleController::class, 'destroy']);
            Route::get('/download/{id}', [CapsuleController::class, 'download']); 
        });

    });

});