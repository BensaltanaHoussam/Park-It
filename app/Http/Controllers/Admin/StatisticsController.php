<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parking;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    public function index(): JsonResponse
    {
        $basicStats = [
            'total_parkings' => Parking::count(),
            'total_reservations' => Reservation::count(),
            'parkings_overview' => Parking::select('name', 'total_spots', 'available_spots')
                ->get()
                ->map(function ($parking) {
                    return [
                        'name' => $parking->name,
                        'total_spots' => $parking->total_spots,
                        'available_spots' => $parking->available_spots,
                        'occupied_spots' => $parking->total_spots - $parking->available_spots
                    ];
                })
        ];

        return response()->json($basicStats);
    }
}