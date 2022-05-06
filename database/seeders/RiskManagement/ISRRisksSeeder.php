<?php

namespace Database\Seeders\RiskManagement;

use App\Imports\RiskManagement\RiskTemplateImport;
use App\Models\RiskManagement\RisksTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Maatwebsite\Excel\Facades\Excel;

class ISRRisksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (RisksTemplate::where('standard_id', 2)->count() == 0) {
            $filepath = 'database/seeders/RiskManagement/standards/ISR_V2.xlsx';
            $controlsCsvfile = new File($filepath);

            $import = new RiskTemplateImport(2);

            Excel::import($import, $controlsCsvfile);
        }
    }
}
