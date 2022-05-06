<?php

namespace Database\Seeders\RiskManagement;

use App\Imports\RiskManagement\RiskTemplateImport;
use App\Models\RiskManagement\RisksTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Maatwebsite\Excel\Facades\Excel;

class NCACSCCSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (RisksTemplate::where('standard_id', 5)->count() == 0) {
            $filepath = 'database/seeders/RiskManagement/standards/NCA_CSCC_2019_DASH.xlsx';
            $controlsCsvfile = new File($filepath);

            $import = new RiskTemplateImport(5);

            Excel::import($import, $controlsCsvfile);
        }
    }
}
