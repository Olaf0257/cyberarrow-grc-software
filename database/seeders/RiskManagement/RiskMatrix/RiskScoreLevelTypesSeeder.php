<?php

namespace Database\Seeders\RiskManagement\RiskMatrix;

use Illuminate\Database\Seeder;

class RiskScoreLevelTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(\DB::table('risk_score_matrix_level_types')->get('id')->count() == 0){
            // Default risk matrix score level types
            \DB::table('risk_score_matrix_level_types')->insert([
                [
                    'level' => 3,
                    'is_active' => false
                ],
                [
                    'level' => 4,
                    'is_active' => true,
                ],
                [
                    'level' => 5,
                    'is_active' => false,
                ],
                [
                    'level' => 6,
                    'is_active' => false
                ],
            ]);
        }
    }
}
