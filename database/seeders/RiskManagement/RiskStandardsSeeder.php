<?php

namespace Database\Seeders\RiskManagement;

use Illuminate\Database\Seeder;

class RiskStandardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if( \DB::table('risks_standards')->select('id')->count() == 0){
            // Default risk templates
            \DB::table('risks_standards')->insert([
                ['name' => 'ISO/IEC 27002:2013'],
                ['name' => 'ISR V2'],
                ['name' => 'SAMA Cyber Security Framework'],
                ['name' => 'NCA ECC-1:2018'],
                ['name' => 'NCA CSCC-1:2019'],
            ]);
        }
    }
}
