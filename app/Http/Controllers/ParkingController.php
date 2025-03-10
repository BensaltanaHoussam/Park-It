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
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'total_spots' => 'required',
            'available_spots' => 'required',
        ]);

        return Parking::create([
            'name' => $request->name,
            'location' => $request->location,
            'total_spots' => $request->total_spots,
            'available_spots' => $request->available_spots,

        ]);
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
