<?php

namespace Database\Seeders;

use App\Models\PointInPound;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PointInPoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PointInPound::factory()->count(10)->create();
    }
}
