<?php

namespace Database\Seeders;

use App\Models\AchievementUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AchievementUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AchievementUser::factory()->count(10)->create();
    }
}
