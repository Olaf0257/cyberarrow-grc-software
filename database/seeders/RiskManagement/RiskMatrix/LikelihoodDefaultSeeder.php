<?php

namespace Database\Seeders\RiskManagement\RiskMatrix;

use Illuminate\Database\Seeder;

class LikelihoodDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (\DB::table('risk_score_matrix_likelihoods')->count() == 0) {
            // Default risk matrix likelihoods
            \DB::table('risk_score_matrix_likelihoods')->insert([
                ['name' => 'Rare', 'index' => 0],
                ['name' => 'Unlikely', 'index' => 1],
                ['name' => 'Possible', 'index' => 2],
                ['name' => 'Likely', 'index' => 3],
                ['name' => 'Almost certain', 'index' => 4]
            ]);
        }
    }
}
