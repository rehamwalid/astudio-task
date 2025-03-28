<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Location::insert([
            ['city' => 'New York', 'state' => 'NY', 'country' => 'USA'],
            ['city' => 'San Francisco', 'state' => 'CA', 'country' => 'USA'],
            ['city' => 'Los Angeles', 'state' => 'CA', 'country' => 'USA'],
            ['city' => 'Chicago', 'state' => 'IL', 'country' => 'USA'],
            ['city' => 'London', 'state' => 'England', 'country' => 'UK'],
            ['city' => 'Remote', 'state' => null, 'country' => null],
            ['city' => 'Berlin', 'state' => null, 'country' => 'Germany'],
            ['city' => 'Sydney', 'state' => 'NSW', 'country' => 'Australia'],
            ['city' => 'Toronto', 'state' => 'ON', 'country' => 'Canada'],
            ['city' => 'Paris', 'state' => null, 'country' => 'France'],
        ]);
    }
}
