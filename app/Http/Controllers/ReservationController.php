<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'user_id' => 'required',
            'parking_id' => 'required',
            'arrival_time' => 'required',
            'departure_time' => 'required',
        ]);

        $parking = Parking::find($request->parking_id);

        if ($parking->available_spots > 0){
            $reservation = Reservation::create([
                'user_id' => $request->user_id,
                'parking_id' => $request->parking_id,
                'arrival_time' => $request->arrival_time,
                'departure_time' => $request->departure_time,
            ]);

            $parking->decrement('available_spots');
            return response()->json(['message'=>'Reservation created successfully','reservation'=>$reservation], 201);
        }

        return response()->json(['message'=>'No available spots'], 400);

      
    }
}
