<?php

namespace Database\Seeders\Compliance;

use App\Imports\Compliance\ControlsImport;
use App\Models\Compliance\Standard;
use App\Models\Compliance\StandardControl;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Maatwebsite\Excel\Facades\Excel;

class PciDssFixesSeeder extends Seeder
{
    public $standardBasePath = 'database/seeders/Compliance/standards/';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultStandardsToBeSeeded = $this->getWrongStandards();
        foreach ($defaultStandardsToBeSeeded as $key => $defaultStandard) {
            if (isset($defaultStandard['name']) && isset($defaultStandard['version']) && isset($defaultStandard['controls_path']) && isset($defaultStandard['controls_separator'])) {
                $standard = Standard::where('name', $defaultStandard['name'])->where('version', $defaultStandard['version'])->first();
                if ($standard) {
                    StandardControl::where('standard_id', $standard->id)->delete();
                } else {
                    $standard = Standard::create([
                        'name' => $defaultStandard['name'],
                        'version' => $defaultStandard['version'],
                        'is_default' => 1,
                    ]);
                }

                $controlsCsvfile = new File($defaultStandard['controls_path']);
                $file_data = file_get_contents($controlsCsvfile);

                /* When file encoding is not UTF-8.  Converting file encoding to utf-8 and rewriting the same file */
                if (!mb_check_encoding($file_data, 'UTF-8')) {
                    $utf8_file_data = utf8_encode($file_data);
                    file_put_contents($controlsCsvfile, $utf8_file_data);
                }

                $import = new ControlsImport($standard, $defaultStandard['controls_separator'], true);

                Excel::import($import, $controlsCsvfile);
            }
        }
    }

    public function getWrongStandards()
    {
        return [
            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire P2PE',
                'version' => 'V3.2.1',
                'controls_path' => $this->standardBasePath . 'PCI DSS - Self-Assessment Questionnaire P2PE v3.2.1-DOT.csv',
                'controls_separator' => '.',
            ],
            [
                'name' => 'PCI DSS Appendix A',
                'version' => 'V3.2.1',
                'controls_path' => $this->standardBasePath . 'PCI DSS Appendix A v3.2.1-SPACE.csv',
                'controls_separator' => '.',
            ]
        ];
    }
}
