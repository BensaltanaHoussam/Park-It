<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParkingController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/parkings', [ParkingController::class, 'index']);
    Route::post('/parkings', [ParkingController::class, 'store']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/user', [AuthController::class, 'user']); 
    Route::get('/search-parking', [ParkingController::class, 'search']);
});
