<?php

namespace Database\Seeders\Compliance;

use Illuminate\Database\Seeder;
use App\Models\Compliance\Standard;
use App\Models\Compliance\StandardControl;
use Illuminate\Http\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Compliance\ControlsImport;

class NistCybersecurityFixesSeeder extends Seeder
{
    public $standardBasePath = 'database/seeders/Compliance/standards/';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nist = Standard::where('name', 'NIST Cybersecurity Framework')->where('version', 'V1.1')->first();
        if(isset($nist->id)){
            StandardControl::where('standard_id',$nist->id)->delete();

            $controlsCsvfile = new File($this->standardBasePath.'NIST Cybersecurity Framework v1.1-DOT.csv');
            $file_data = file_get_contents($controlsCsvfile);

            /* When file encoding is not UTF-8.  Converting file encoding to utf-8 and rewriting the same file */
            if (!mb_check_encoding($file_data, 'UTF-8')) {
                $utf8_file_data = utf8_encode($file_data);
                file_put_contents($controlsCsvfile, $utf8_file_data);
            }

            $import = new ControlsImport($nist, '.', true);

            Excel::import($import, $controlsCsvfile);
        }
    }
}
