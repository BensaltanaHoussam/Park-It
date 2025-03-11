<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{


    public function index(Request $request)
    {
        $reservations = Reservation::with('parking')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'current_reservations' => $reservations->where('departure_time', '>=', now()),
            'past_reservations' => $reservations->where('departure_time', '<', now())
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'parking_id' => 'required',
            'arrival_time' => 'required',
            'departure_time' => 'required',
        ]);

        $parking = Parking::find($request->parking_id);

        if ($parking->available_spots > 0) {
            $reservation = Reservation::create([
                'user_id' => $request->user_id,
                'parking_id' => $request->parking_id,
                'arrival_time' => $request->arrival_time,
                'departure_time' => $request->departure_time,
            ]);

            $parking->decrement('available_spots');
            return response()->json(['message' => 'Reservation created successfully', 'reservation' => $reservation], 201);
        }

        return response()->json(['message' => 'No available spots'], 400);


    }

    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'arrival_time' => 'required',
            'departure_time' => 'required',
        ]);

        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reservation->update([
            'arrival_time' => $request->arrival_time,
            'departure_time' => $request->departure_time,
        ]);

        return response()->json([
            'message' => 'Reservation updated successfully',
            'reservation' => $reservation
        ]);
    }

    public function destroy(Reservation $reservation)
    {

        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $parking = Parking::find($reservation->parking_id);
        $parking->increment('available_spots');

        $reservation->delete();

        return response()->json(['message' => 'Reservation cancelled successfully']);
    }
}
