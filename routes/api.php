<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CapsuleController;

Route::group(["prefix" => "v1"], function () {

    Route::group(["prefix" => "guest"], function () {
        Route::post("/login", [AuthController::class, 'login']);
        Route::post("/register", [AuthController::class, 'register']);
    });

    Route::group(["middleware" => "auth:api"], function () {

        Route::group(["prefix" => "capsules"], function () {
            Route::get("/", [CapsuleController::class, 'index']); 
            Route::post("/", [CapsuleController::class, 'store']); 
            Route::get("/public", [CapsuleController::class, 'publicWall']); 
            Route::delete("/{id}", [CapsuleController::class, 'destroy']); 
        });

    });

});