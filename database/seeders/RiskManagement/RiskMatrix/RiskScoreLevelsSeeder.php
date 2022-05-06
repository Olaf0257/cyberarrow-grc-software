<?php

namespace Database\Seeders\RiskManagement\RiskMatrix;

use Illuminate\Database\Seeder;

class RiskScoreLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if( \DB::table('risk_score_matrix_levels')->select('id')->count() == 0){
            // Default risk matrix score level types
            \DB::table('risk_score_matrix_levels')->insert([
                // 3 level
                [
                    'name' => 'Low Risk',
                    'max_score' => 3,
                    'color' => '#00FF25',
                    'level_type' => 3,
                ],
                [
                    'name' => 'Moderate Risk',
                    'max_score' => 10,
                    'color' => '#F2FF00',
                    'level_type' => 3,
                ],
                [
                    'name' => 'High Risk',
                    'max_score' => null,
                    'color' => '#FF0000',
                    'level_type' => 3,
                ],

                // 4 level
                [
                    'name' => 'Low Risk',
                    'max_score' => 3,
                    'color' => '#7DE64F',
                    'level_type' => 4,
                ],
                [
                    'name' => 'Moderate Risk',
                    'max_score' => 10,
                    'color' => '#F2FF00',
                    'level_type' => 4,
                ],
                [
                    'name' => 'High Risk',
                    'max_score' => 16,
                    'color' => '#FCCF00',
                    'level_type' => 4,
                ],
                [
                    'name' => 'Extreme Risk',
                    'max_score' => null,
                    'color' => '#FF0000',
                    'level_type' => 4,
                ],

                //5 Levels
                [
                    'name' => 'Low Risk',
                    'max_score' => 3,
                    'color' => '#7DE64F',
                    'level_type' => 5,
                ],
                [
                    'name' => 'Moderate Risk',
                    'max_score' => 10,
                    'color' => '#F2FF00',
                    'level_type' => 5,
                ],
                [
                    'name' => 'High Risk',
                    'max_score' => 16,
                    'color' => '#FCCF00',
                    'level_type' => 5,
                ],
                [
                    'name' => 'Extreme Risk',
                    'max_score' => 24,
                    'color' => '#FF0000',
                    'level_type' => 5,
                ],
                [
                    'name' => 'Super Extreme risk',
                    'max_score' => null,
                    'color' => '#9B0000',
                    'level_type' => 5,
                ],

                // 6 levels:
                [
                    'name' => 'Very Low Risk',
                    'max_score' => 3,
                    'color' => '#00FFFF',
                    'level_type' => 6,
                ],
                [
                    'name' => 'Low Risk',
                    'max_score' => 10,
                    'color' => '#00FF25',
                    'level_type' => 6,
                ],
                [
                    'name' => 'Moderate Risk',
                    'max_score' => 16,
                    'color' => '#F2FF00',
                    'level_type' => 6,
                ],
                [
                    'name' => 'High Risk',
                    'max_score' => 23,
                    'color' => '#FFA000',
                    'level_type' => 6,
                ],
                [
                    'name' => 'Very High Risk',
                    'max_score' => 24,
                    'color' => '#FF0000',
                    'level_type' => 6,
                ],
                [
                    'name' => 'Extremely High Risk',
                    'max_score' => null,
                    'color' => '#9B0000',
                    'level_type' => 6
                ],
            ]);
        }
    }
}