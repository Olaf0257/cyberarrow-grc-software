<?php

namespace Database\Seeders\RiskManagement;

use App\Imports\RiskManagement\RiskTemplateImport;
use App\Models\RiskManagement\RisksTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Maatwebsite\Excel\Facades\Excel;

class SamaCyberSecurityFrameworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (RisksTemplate::where('standard_id', 3)->count() == 0) {
            $filepath = 'database/seeders/RiskManagement/standards/Sama_Cybersecurity_Framework_DOT.xlsx';
            $controlsCsvfile = new File($filepath);

            $import = new RiskTemplateImport(3);

            Excel::import($import, $controlsCsvfile);
        }
    }
}
