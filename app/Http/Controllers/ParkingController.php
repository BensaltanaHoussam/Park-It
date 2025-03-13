<?php

namespace App\Http\Controllers;

use App\Models\Parking;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;

class ParkingController extends Controller
{
    public function index()
    {
        return Parking::all();
    }

    public function store(Request $request)
    {
    
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'total_spots' => 'required|integer|min:1',
        ]);

        $parking = Parking::create($validatedData);
        return response()->json(['message' => 'Parking created successfully', 'parking' => $parking], 201);
    }


    public function update(Request $request, Parking $parking)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'total_spots' => 'required|integer|min:1',
            'available_spots' => 'required|integer|min:0|lte:total_spots',
        ]);

        $parking->update($validatedData);
        return response()->json(['message' => 'Parking updated successfully', 'parking' => $parking]);
    }


    public function destroy(Parking $parking)
    {
        if ($parking->reservations()->where('departure_time', '>=', now())->exists()) {
            return response()->json(['message' => 'Cannot delete parking with active reservations'], 400);
        }

        $parking->delete();
        return response()->json(['message' => 'Parking deleted successfully']);
    }


    public function search(Request $request)
    {
        $request->validate([
            'location' => 'required|string'
        ]);


        $parkings = Parking::where('location', 'LIKE', "%{$request->location}%")
            ->where('available_spots', '>', 0)
            ->get();

        return response()->json($parkings);
    }
}
