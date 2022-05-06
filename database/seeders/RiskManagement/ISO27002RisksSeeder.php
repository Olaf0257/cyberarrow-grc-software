<?php

namespace Database\Seeders\RiskManagement;

use App\Imports\RiskManagement\RiskTemplateImport;
use App\Models\RiskManagement\RisksTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Maatwebsite\Excel\Facades\Excel;

class ISO27002RisksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (RisksTemplate::where('standard_id', 1)->count() == 0) {
            $filepath = 'database/seeders/RiskManagement/standards/ISO_27002.xlsx';
            $controlsCsvfile = new File($filepath);

            $import = new RiskTemplateImport(1);

            Excel::import($import, $controlsCsvfile);
        }
    }
}
