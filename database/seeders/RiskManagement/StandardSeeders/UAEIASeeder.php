<?php

namespace Database\Seeders\RiskManagement\StandardSeeders;

use App\Imports\RiskManagement\RiskTemplateImport;
use App\Models\RiskManagement\RisksTemplate;
use App\Models\RiskManagement\RiskStandard;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Maatwebsite\Excel\Facades\Excel;

class UAEIASeeder extends Seeder
{

    public $standardFileBasePath = 'database/seeders/RiskManagement/standards/';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultStandard = [
            'name' => 'UAE IA',
            'file_path' => $this->standardFileBasePath.'UAE_IA.xlsx',
        ];

        $standard = RiskStandard::firstOrCreate([
            'name' => $defaultStandard['name'],
        ]);

        if (RisksTemplate::where('standard_id', $standard->id)->count() == 0) {
            $controlsCsvfile = new File($defaultStandard['file_path']);

            $import = new RiskTemplateImport($standard->id);

            Excel::import($import, $controlsCsvfile);
        }
    }
}
