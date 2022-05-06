<?php

namespace Database\Seeders\Compliance;

use Illuminate\Http\File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Compliance\Standard;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Compliance\ControlsImport;

class DefaultComplianceStandardsSeeder extends Seeder
{
    public $standardBasePath = 'database/seeders/Compliance/standards/';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if(!DB::table('compliance_standards')->exists())
        {
            $defaultStandardsToBeSeeded = $this->getDefaultStandards();

            foreach ($defaultStandardsToBeSeeded as $key => $defaultStandard) {
                if (isset($defaultStandard['name']) && isset($defaultStandard['version']) && isset($defaultStandard['controls_path']) && isset($defaultStandard['controls_seperator'])) {
                    $found = Standard::where('name', $defaultStandard['name'])->where('version', $defaultStandard['version'])->first();
                    if (!$found) {
                        $standard = Standard::create([
                            'name' => $defaultStandard['name'],
                            'version' => $defaultStandard['version'],
                            'is_default' => 1,
                        ]);
    
                        $controlsCsvfile =  new File($defaultStandard['controls_path']);
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
     
    }

    public function getDefaultStandards()
    {
        return [
            [
                'name' => 'CIS Critical Security Controls Group 1',
                'version' => 'V7.1',
                'controls_path' => $this->standardBasePath.'CIS Critical Security Controls Group 1 v7.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'CIS Critical Security Controls Group 2',
                'version' => 'V7.1',
                'controls_path' => $this->standardBasePath.'CIS Critical Security Controls Group 2 v7.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'CIS Critical Security Controls Group 3',
                'version' => 'V7.1',
                'controls_path' => $this->standardBasePath.'CIS Critical Security Controls Group 3 v7.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'Cloud Computing Compliance Controls Catalogue',
                'version' => 'V9.0',
                'controls_path' => $this->standardBasePath.'Cloud Computing Compliance Controls Catalogue v9.2017-SPACE.csv',
                'controls_seperator' => '&nbsp;',
            ],

            [
                'name' => 'Cloud Security Alliance - CCM',
                'version' => 'V3.1',
                'controls_path' => $this->standardBasePath.'Cloud Security Alliance - CCM v3.1-SPACE.csv',
                'controls_seperator' => '&nbsp;',
            ],

            [
                'name' => 'HIPAA Privacy and Breach Rule',
                'version' => 'V1.0',
                'controls_path' => $this->standardBasePath.'HIPAA Privacy and Breach Rule v1.0 v2-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'HIPAA Security Rule',
                'version' => 'V1.0',
                'controls_path' => $this->standardBasePath.'HIPAA Security Rule v1.0 v2-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'Internet of Things Assessment Questionnaire',
                'version' => 'V3.0',
                'controls_path' => $this->standardBasePath.'Internet of Things Assessment Questionnaire v3.0-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'ISO/IEC 27001-2:2013',
                'version' => 'V1.0',
                'controls_path' => $this->standardBasePath.'ISO 27001-2 2013-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'ISO/IEC 27035-1:2016',
                'version' => 'V1.0',
                'controls_path' => $this->standardBasePath.'ISO 27035-1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'NCA CSCC-1:2019',
                'version' => 'V1.0',
                'controls_path' => $this->standardBasePath.'NCA CSCC –1 2019-DASH.csv',
                'controls_seperator' => '-',
            ],

            [
                'name' => 'NCA ECC-1:2018',
                'version' => 'V1.0',
                'controls_path' => $this->standardBasePath.'NCA ECC – 1 2018-DASH.csv',
                'controls_seperator' => '-',
            ],

            [
                'name' => 'NIST Cybersecurity Framework',
                'version' => 'V1.1',
                'controls_path' => $this->standardBasePath.'NCA ECC – 1 2018-DASH.csv',
                'controls_seperator' => '-',
            ],

            [
                'name' => 'NIST SP 800-171 Appendix E',
                'version' => 'V1.0',
                'controls_path' => $this->standardBasePath.'NIST SP 800-171 Appendix E-DASH.csv',
                'controls_seperator' => '-',
            ],

            [
                'name' => 'NIST SP 800-171 A',
                'version' => 'V1.0',
                'controls_path' => $this->standardBasePath.'NIST SP 800-171 A_-SPACE.csv',
                'controls_seperator' => '&nbsp;',
            ],

            [
                'name' => 'NIST SP 800-53 High-Impact Baseline',
                'version' => 'V4.0',
                'controls_path' => $this->standardBasePath.'NIST SP 800-53 High-Impact Baseline rev4-DASH.csv',
                'controls_seperator' => '-',
            ],

            [
                'name' => 'NIST SP 800-53 Low-Impact Baseline',
                'version' => 'V4.0',
                'controls_path' => $this->standardBasePath.'NIST SP 800-53 Low-Impact Baseline rev4-DASH.csv',
                'controls_seperator' => '-',
            ],

            [
                'name' => 'NIST SP 800-53 Moderate-Impact Baseline',
                'version' => 'V4.0',
                'controls_path' => $this->standardBasePath.'NIST SP 800-53 Moderate-Impact Baseline rev4-DASH.csv',
                'controls_seperator' => '-',
            ],

            [
                'name' => 'OWASP Level 1',
                'version' => 'V4.0',
                'controls_path' => $this->standardBasePath.'OWASP Level 1 v4.0-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'OWASP Level 2',
                'version' => 'V4.0',
                'controls_path' => $this->standardBasePath.'OWASP Level 2 v4.0-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'OWASP Level 3',
                'version' => 'V4.0',
                'controls_path' => $this->standardBasePath.'OWASP Level 3 v4.0-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire A',
                'version' => 'V3.2.1',
                'controls_path' => $this->standardBasePath.'PCI DSS - Self-Assessment Questionnaire A v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire A-EP',
                'version' => 'V3.2.1',
                'controls_path' => $this->standardBasePath.'PCI DSS - Self-Assessment Questionnaire A-EP v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire B',
                'version' => 'V3.2.1',
                'controls_path' => $this->standardBasePath.'PCI DSS - Self-Assessment Questionnaire B v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire B-IB',
                'version' => 'V3.2.1',
                'controls_path' => $this->standardBasePath.'PCI DSS - Self-Assessment Questionnaire B-IB v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire C',
                'version' => 'V3.2.1',
                'controls_path' => $this->standardBasePath.'PCI DSS - Self-Assessment Questionnaire C v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire C-VT',
                'version' => 'V3.2.1',
                'controls_path' => $this->standardBasePath.'PCI DSS - Self-Assessment Questionnaire C-VT v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire D Merchants',
                'version' => 'V3.2.1',
                'controls_path' => $this->standardBasePath.'PCI DSS - Self-Assessment Questionnaire D Merchants v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire D Service Providers',
                'version' => 'V3.2.1',
                'controls_path' => $this->standardBasePath.'PCI DSS - Self-Assessment Questionnaire D Service Providers v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire P2PE',
                'version' => 'V3.2.1',
                'controls_path' => $this->standardBasePath.'PCI DSS - Self-Assessment Questionnaire D Service Providers v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS Appendix A',
                'version' => 'V3.2.1',
                'controls_path' => $this->standardBasePath.'PCI DSS - Self-Assessment Questionnaire P2PE v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS',
                'version' => 'V3.2.1',
                'controls_path' => $this->standardBasePath.'PCI DSS v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'Sarbanes Oxley Act',
                'version' => 'V7.0',
                'controls_path' => $this->standardBasePath.'Sarbanes Oxley Act v7.2002-DOT.csv',
                'controls_seperator' => '.',
            ],
            [
                'name' => 'Sarbanes Oxley Act',
                'version' => 'V7.0',
                'controls_path' => $this->standardBasePath.'Sarbanes Oxley Act v7.2002-DOT.csv',
                'controls_seperator' => '.',
            ],
            [
                'name' => 'SAMA Business Continuity Management Framework',
                'version' => 'V1.0',
                'controls_path' => $this->standardBasePath.'Sama BCMS Framework-DOT.csv',
                'controls_seperator' => '.',
            ],
            [
                'name' => 'SAMA Cyber Security Framework',
                'version' => 'V1.0',
                'controls_path' => $this->standardBasePath.'Sama Cybersecurity Framework-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'ISR V2',
                'version' => 'V2.0',
                'controls_path' => $this->standardBasePath.'ISR V2-DOT.csv',
                'controls_seperator' => '.',
            ],
        ];
    }
}
