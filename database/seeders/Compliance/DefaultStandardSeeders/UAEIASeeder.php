<?php

namespace Database\Seeders\Compliance\DefaultStandardSeeders;

use App\Imports\Compliance\ControlsImport;
use App\Models\Compliance\Standard;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Maatwebsite\Excel\Facades\Excel;

class UAEIASeeder extends Seeder
{
    public $standardBasePath = 'database/seeders/Compliance/standards/';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultStandard = [
            'name' => 'UAE IA',
            'version' => 'V1.0',
            'controls_path' => $this->standardBasePath.'UAE_IA-_DOT.csv',
            'controls_seperator' => '.',
        ];

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
