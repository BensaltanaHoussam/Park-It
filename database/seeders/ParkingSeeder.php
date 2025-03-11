<?php

namespace Database\Seeders;

use App\Models\Parking;
use Illuminate\Database\Seeder;

class ParkingSeeder extends Seeder
{
    public function run()
    {
        Parking::create([
            'name' => 'Parking A',
            'location' => 'Downtown',
            'total_spots' => 50,
            'available_spots' => 50,
        ]);

        Parking::create([
            'name' => 'Parking B',
            'location' => 'City Center',
            'total_spots' => 100,
            'available_spots' => 100,
        ]);

        Parking::create([
            'name' => 'Parking C',
            'location' => 'Shopping Mall',
            'total_spots' => 75,
            'available_spots' => 75,
        ]);

        Parking::create([
            'name' => 'Parking D',
            'location' => 'Train Station',
            'total_spots' => 200,
            'available_spots' => 200,
        ]);
    }
}