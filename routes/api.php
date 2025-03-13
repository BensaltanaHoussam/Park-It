<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParkingController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', [AuthController::class, 'user']);

    // Parking routes - view only
    Route::get('/parkings', [ParkingController::class, 'index']);
    Route::get('/search-parking', [ParkingController::class, 'search']);

    // Reservation routes
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::put('/reservations/{reservation}', [ReservationController::class, 'update']);
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy']);
    Route::post('/check-availability', [ReservationController::class, 'isAvailable']); 


    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::post('/parkings', [ParkingController::class, 'store']);
        Route::put('/parkings/{parking}', [ParkingController::class, 'update']);
        Route::delete('/parkings/{parking}', [ParkingController::class, 'destroy']);
        Route::get('/admin/statistics', [App\Http\Controllers\Admin\StatisticsController::class, 'index']); 
    });


});

