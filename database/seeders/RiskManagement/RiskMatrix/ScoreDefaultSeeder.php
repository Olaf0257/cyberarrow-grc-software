<?php

namespace Database\Seeders\RiskManagement\RiskMatrix;

use Illuminate\Database\Seeder;

class ScoreDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(\DB::table('risk_score_matrix_scores')->select('id')->count() == 0){
            // Default risk matrix scores
            \DB::table('risk_score_matrix_scores')->insert([
                // 1st row
                ['score' => 1, 'likelihood_index' => 0, 'impact_index' => 0],
                ['score' => 2, 'likelihood_index' => 0, 'impact_index' => 1],
                ['score' => 3, 'likelihood_index' => 0, 'impact_index' => 2],
                ['score' => 4, 'likelihood_index' => 0, 'impact_index' => 3],
                ['score' => 5, 'likelihood_index' => 0, 'impact_index' => 4],
                // 2nd row
                ['score' => 2, 'likelihood_index' => 1, 'impact_index' => 0],
                ['score' => 4, 'likelihood_index' => 1, 'impact_index' => 1],
                ['score' => 6, 'likelihood_index' => 1, 'impact_index' => 2],
                ['score' => 8, 'likelihood_index' => 1, 'impact_index' => 3],
                ['score' => 10, 'likelihood_index' => 1, 'impact_index' => 4],
                // 3rd row
                ['score' => 3, 'likelihood_index' => 2, 'impact_index' => 0],
                ['score' => 6, 'likelihood_index' => 2, 'impact_index' => 1],
                ['score' => 9, 'likelihood_index' => 2, 'impact_index' => 2],
                ['score' => 12, 'likelihood_index' => 2, 'impact_index' => 3],
                ['score' => 15, 'likelihood_index' => 2, 'impact_index' => 4],
                // 4th row
                ['score' => 4, 'likelihood_index' => 3, 'impact_index' => 0],
                ['score' => 8, 'likelihood_index' => 3, 'impact_index' => 1],
                ['score' => 12, 'likelihood_index' => 3, 'impact_index' => 2],
                ['score' => 16, 'likelihood_index' => 3, 'impact_index' => 3],
                ['score' => 20, 'likelihood_index' => 3, 'impact_index' => 4],
                // 5th row
                ['score' => 5, 'likelihood_index' => 4, 'impact_index' => 0],
                ['score' => 10, 'likelihood_index' => 4, 'impact_index' => 1],
                ['score' => 15, 'likelihood_index' => 4, 'impact_index' => 2],
                ['score' => 20, 'likelihood_index' => 4, 'impact_index' => 3],
                ['score' => 25, 'likelihood_index' => 4, 'impact_index' => 4],
            ]);
        }
    }
}
