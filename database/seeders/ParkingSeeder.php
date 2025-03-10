<?php

namespace Database\Seeders;

use App\Models\Parking;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParkingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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
    }
}
