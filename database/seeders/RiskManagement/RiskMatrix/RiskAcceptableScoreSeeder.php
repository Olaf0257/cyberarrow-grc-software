<?php

namespace Database\Seeders\RiskManagement\RiskMatrix;

use Illuminate\Database\Seeder;

class RiskAcceptableScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(\DB::table('risk_acceptable_score')->count() == 0){
            \DB::table('risk_acceptable_score')->insert([
                'score' => 3
            ]);
        }
    }
}
