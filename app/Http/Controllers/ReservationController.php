<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use App\Models\Reservation;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'parking_id' => 'required|exists:parkings,id',
            'arrival_time' => 'required|date|after:now',
            'departure_time' => 'required|date|after:arrival_time',
        ]);

        $parking = Parking::findOrFail($request->parking_id);
        $arrivalTime = Carbon::parse($request->arrival_time);
        $departureTime = Carbon::parse($request->departure_time);

        // Check for conflicting reservations
        $conflictingReservations = Reservation::where('parking_id', $request->parking_id)
            ->where(function ($query) use ($arrivalTime, $departureTime) {
                $query->whereBetween('arrival_time', [$arrivalTime, $departureTime])
                    ->orWhereBetween('departure_time', [$arrivalTime, $departureTime])
                    ->orWhere(function ($q) use ($arrivalTime, $departureTime) {
                        $q->where('arrival_time', '<=', $arrivalTime)
                            ->where('departure_time', '>=', $departureTime);
                    });
            })
            ->count();

        if ($conflictingReservations > 0) {
            return response()->json([
                'message' => 'Parking is not available for the selected time slot'
            ], 400);
        }

        // Check if parking has available spots
        if ($parking->total_spots <= 0) {
            return response()->json([
                'message' => 'No parking spots available'
            ], 400);
        }

        // Create reservation and decrement total spots
        $reservation = DB::transaction(function () use ($parking, $request, $arrivalTime, $departureTime) {
            $reservation = Reservation::create([
                'user_id' => auth()->id(),
                'parking_id' => $request->parking_id,
                'arrival_time' => $arrivalTime,
                'departure_time' => $departureTime,
            ]);

            $parking->decrement('total_spots');

            return $reservation;
        });

        return response()->json([
            'message' => 'Reservation created successfully',
            'reservation' => $reservation,
            'remaining_spots' => $parking->total_spots - 1
        ], 201);
    }

    public function isAvailable(Request $request)
    {
        $request->validate([
            'parking_id' => 'required|exists:parkings,id',
            'arrival_time' => 'required|date',
            'departure_time' => 'required|date|after:arrival_time',
        ]);

        $arrivalTime = Carbon::parse($request->arrival_time);
        $departureTime = Carbon::parse($request->departure_time);


        $isAvailable = Reservation::where('parking_id', $request->parking_id)
            ->where(function ($query) use ($arrivalTime, $departureTime) {
                $query->whereBetween('arrival_time', [$arrivalTime, $departureTime])
                    ->orWhereBetween('departure_time', [$arrivalTime, $departureTime])
                    ->orWhere(function ($q) use ($arrivalTime, $departureTime) {
                        $q->where('arrival_time', '<=', $arrivalTime)
                            ->where('departure_time', '>=', $departureTime);
                    });
            })
            ->doesntExist();

        return response()->json([
            'is_available' => $isAvailable,
            'time_slot' => [
                'arrival' => $arrivalTime->format('Y-m-d H:i:s'),
                'departure' => $departureTime->format('Y-m-d H:i:s')
            ]
        ]);
    }
}