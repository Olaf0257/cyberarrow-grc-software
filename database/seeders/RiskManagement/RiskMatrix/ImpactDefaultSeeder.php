<?php

namespace Database\Seeders\RiskManagement\RiskMatrix;

use Illuminate\Database\Seeder;

class ImpactDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default risk matrix impacts
        if(!\DB::table('risk_score_matrix_impacts')->exists())
        {
            \DB::table('risk_score_matrix_impacts')->insert([
                ['name' => 'Negligible', 'index' => 0],
                ['name' => 'Minor', 'index' => 1],
                ['name' => 'Moderate', 'index' => 2],
                ['name' => 'Major', 'index' => 3],
                ['name' => 'Catastrophic', 'index' => 4]
            ]);
        }
        
    }
}
