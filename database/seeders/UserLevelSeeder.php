<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserLevel;

class UserLevelSeeder extends Seeder
{
    public function run(): void
    {
        UserLevel::truncate();

        $levels = [
            ['level' => 'bronze', 'min_points' => 0],
            ['level' => 'silver', 'min_points' => 4000],
            ['level' => 'gold',   'min_points' => 8000],
        ];

        foreach ($levels as $level) {
            UserLevel::create($level);
        }
    }
}
