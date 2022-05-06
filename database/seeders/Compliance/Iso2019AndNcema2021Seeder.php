<?php

namespace Database\Seeders\Compliance;

use Illuminate\Http\File;
use Illuminate\Database\Seeder;
use App\Models\Compliance\Standard;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Compliance\ControlsImport;

class Iso2019AndNcema2021Seeder extends Seeder
{
    public $standardBasePath = 'database/seeders/Compliance/standards/';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultStandardsToBeSeeded = $this->getIsoAndNcemaStandards();

        foreach ($defaultStandardsToBeSeeded as $key => $defaultStandard) {
            if (isset($defaultStandard['name']) && isset($defaultStandard['version']) && isset($defaultStandard['controls_path']) && isset($defaultStandard['controls_seperator'])) {
                $found = Standard::where('name', $defaultStandard['name'])->where('version', $defaultStandard['version'])->first();
                if (!$found) {
                    $standard = Standard::create([
                        'name' => $defaultStandard['name'],
                        'version' => $defaultStandard['version'],
                        'is_default' => 1,
                    ]);

                    $controlsCsvfile = new File($defaultStandard['controls_path']);
                    $file_data = file_get_contents($controlsCsvfile);

                    /* When file encoding is not UTF-8.  Converting file encoding to utf-8 and rewriting the same file */
                    if (!mb_check_encoding($file_data, 'UTF-8')) {
                        $utf8_file_data = utf8_encode($file_data);
                        file_put_contents($controlsCsvfile, $utf8_file_data);
                    }

                    $import = new ControlsImport($standard, $defaultStandard['controls_seperator'], true);

                    Excel::import($import, $controlsCsvfile);
                }
            }
        }
    }
    public function getIsoAndNcemaStandards()
    {
        return [
            [
                'name' => 'ISO 22301:2019',
                'version' => 'V1.0',
                'controls_path' => $this->standardBasePath.'ISO 22301-2019-dot.csv',
                'controls_seperator' => '.',
            ],
            [
                'name' => 'AE/SCNS/NCEMA 7000:2021',
                'version' => 'V1.0',
                'controls_path' => $this->standardBasePath.'NCEMA 7000-2021-dot.csv',
                'controls_seperator' => '.',
            ]
        ];
    }
}
