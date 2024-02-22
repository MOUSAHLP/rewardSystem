<?php

namespace Database\Seeders;

use App\Models\PointInSyrianPound;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PointInSyrianPoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PointInSyrianPound::factory()->count(10)->create();
    }
}
