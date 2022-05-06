<?php

namespace App\Console\Commands;

use App\Models\Compliance\Project;
use App\Models\Compliance\Standard;
use App\Models\Compliance\StandardControl;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;


class SyncControls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'standards:sync-controls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is responsible of adding all missing controls from default standards';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getStandards()
    {

        return [
            [
                'name' => 'CIS Critical Security Controls Group 1',
                'version' => 'V7.1',
                'controls_path' => 'database/seeders/Compliance/standards/CIS Critical Security Controls Group 1 v7.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'CIS Critical Security Controls Group 2',
                'version' => 'V7.1',
                'controls_path' => 'database/seeders/Compliance/standards/CIS Critical Security Controls Group 2 v7.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'CIS Critical Security Controls Group 3',
                'version' => 'V7.1',
                'controls_path' => 'database/seeders/Compliance/standards/CIS Critical Security Controls Group 3 v7.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'Cloud Computing Compliance Controls Catalogue',
                'version' => 'V9.0',
                'controls_path' => 'database/seeders/Compliance/standards/Cloud Computing Compliance Controls Catalogue v9.2017-SPACE.csv',
                'controls_seperator' => '&nbsp;',
            ],

            [
                'name' => 'Cloud Security Alliance - CCM',
                'version' => 'V3.1',
                'controls_path' => 'database/seeders/Compliance/standards/Cloud Security Alliance - CCM v3.1-SPACE.csv',
                'controls_seperator' => '&nbsp;',
            ],

            [
                'name' => 'HIPAA Privacy and Breach Rule',
                'version' => 'V1.0',
                'controls_path' => 'database/seeders/Compliance/standards/HIPAA Privacy and Breach Rule v1.0 v2-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'HIPAA Security Rule',
                'version' => 'V1.0',
                'controls_path' => 'database/seeders/Compliance/standards/HIPAA Security Rule v1.0 v2-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'Internet of Things Assessment Questionnaire',
                'version' => 'V3.0',
                'controls_path' => 'database/seeders/Compliance/standards/Internet of Things Assessment Questionnaire v3.0-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'ISO/IEC 27001-2:2013',
                'version' => 'V1.0',
                'controls_path' => 'database/seeders/Compliance/standards/ISO 27001-2 2013-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'ISO/IEC 27035-1:2016',
                'version' => 'V1.0',
                'controls_path' => 'database/seeders/Compliance/standards/ISO 27035-1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'NCA CSCC-1:2019',
                'version' => 'V1.0',
                'controls_path' => 'database/seeders/Compliance/standards/NCA CSCC –1 2019-DASH.csv',
                'controls_seperator' => '-',
            ],

            [
                'name' => 'NCA ECC-1:2018',
                'version' => 'V1.0',
                'controls_path' => 'database/seeders/Compliance/standards/NCA ECC – 1 2018-DASH.csv',
                'controls_seperator' => '-',
            ],

            [
                'name' => 'NIST Cybersecurity Framework',
                'version' => 'V1.1',
                'controls_path' => 'database/seeders/Compliance/standards/NIST Cybersecurity Framework v1.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'NIST SP 800-171 Appendix E',
                'version' => 'V1.0',
                'controls_path' => 'database/seeders/Compliance/standards/NIST SP 800-171 Appendix E-DASH.csv',
                'controls_seperator' => '-',
            ],

            [
                'name' => 'NIST SP 800-171 A',
                'version' => 'V1.0',
                'controls_path' => 'database/seeders/Compliance/standards/NIST SP 800-171 A_-SPACE.csv',
                'controls_seperator' => '&nbsp;',
            ],

            [
                'name' => 'NIST SP 800-53 High-Impact Baseline',
                'version' => 'V4.0',
                'controls_path' => 'database/seeders/Compliance/standards/NIST SP 800-53 High-Impact Baseline rev4-DASH.csv',
                'controls_seperator' => '-',
            ],

            [
                'name' => 'NIST SP 800-53 Low-Impact Baseline',
                'version' => 'V4.0',
                'controls_path' => 'database/seeders/Compliance/standards/NIST SP 800-53 Low-Impact Baseline rev4-DASH.csv',
                'controls_seperator' => '-',
            ],

            [
                'name' => 'NIST SP 800-53 Moderate-Impact Baseline',
                'version' => 'V4.0',
                'controls_path' => 'database/seeders/Compliance/standards/NIST SP 800-53 Moderate-Impact Baseline rev4-DASH.csv',
                'controls_seperator' => '-',
            ],

            [
                'name' => 'OWASP Level 1',
                'version' => 'V4.0',
                'controls_path' => 'database/seeders/Compliance/standards/OWASP Level 1 v4.0-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'OWASP Level 2',
                'version' => 'V4.0',
                'controls_path' => 'database/seeders/Compliance/standards/OWASP Level 2 v4.0-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'OWASP Level 3',
                'version' => 'V4.0',
                'controls_path' => 'database/seeders/Compliance/standards/OWASP Level 3 v4.0-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire A',
                'version' => 'V3.2.1',
                'controls_path' => 'database/seeders/Compliance/standards/PCI DSS - Self-Assessment Questionnaire A v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire A-EP',
                'version' => 'V3.2.1',
                'controls_path' => 'database/seeders/Compliance/standards/PCI DSS - Self-Assessment Questionnaire A-EP v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire B',
                'version' => 'V3.2.1',
                'controls_path' => 'database/seeders/Compliance/standards/PCI DSS - Self-Assessment Questionnaire B v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire B-IB',
                'version' => 'V3.2.1',
                'controls_path' => 'database/seeders/Compliance/standards/PCI DSS - Self-Assessment Questionnaire B-IB v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire C',
                'version' => 'V3.2.1',
                'controls_path' => 'database/seeders/Compliance/standards/PCI DSS - Self-Assessment Questionnaire C v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire C-VT',
                'version' => 'V3.2.1',
                'controls_path' => 'database/seeders/Compliance/standards/PCI DSS - Self-Assessment Questionnaire C-VT v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire D Merchants',
                'version' => 'V3.2.1',
                'controls_path' => 'database/seeders/Compliance/standards/PCI DSS - Self-Assessment Questionnaire D Merchants v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire D Service Providers',
                'version' => 'V3.2.1',
                'controls_path' => 'database/seeders/Compliance/standards/PCI DSS - Self-Assessment Questionnaire D Service Providers v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS - Self-Assessment Questionnaire P2PE',
                'version' => 'V3.2.1',
                'controls_path' => 'database/seeders/Compliance/standards/PCI DSS - Self-Assessment Questionnaire P2PE v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS Appendix A',
                'version' => 'V3.2.1',
                'controls_path' => 'database/seeders/Compliance/standards/PCI DSS Appendix A v3.2.1-SPACE.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'PCI DSS',
                'version' => 'V3.2.1',
                'controls_path' => 'database/seeders/Compliance/standards/PCI DSS v3.2.1-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'Sarbanes Oxley Act',
                'version' => 'V7.0',
                'controls_path' => 'database/seeders/Compliance/standards/Sarbanes Oxley Act v7.2002-DOT.csv',
                'controls_seperator' => '.',
            ],
            [
                'name' => 'Sarbanes Oxley Act',
                'version' => 'V7.0',
                'controls_path' => 'database/seeders/Compliance/standards/Sarbanes Oxley Act v7.2002-DOT.csv',
                'controls_seperator' => '.',
            ],
            [
                'name' => 'SAMA Business Continuity Management Framework',
                'version' => 'V1.0',
                'controls_path' => 'database/seeders/Compliance/standards/Sama BCMS Framework-DOT.csv',
                'controls_seperator' => '.',
            ],
            [
                'name' => 'SAMA Cyber Security Framework',
                'version' => 'V1.0',
                'controls_path' => 'database/seeders/Compliance/standards/Sama Cybersecurity Framework-DOT.csv',
                'controls_seperator' => '.',
            ],

            [
                'name' => 'ISR V2',
                'version' => 'V2.0',
                'controls_path' => 'database/seeders/Compliance/standards/ISR V2-DOT.csv',
                'controls_seperator' => '.',
            ],
            [
                'name' => 'ISO 22301:2019',
                'version' => 'V1.0',
                'controls_path' => 'database/seeders/Compliance/standards/ISO 22301-2019-dot.csv',
                'controls_seperator' => '.',
            ],
            [
                'name' => 'AE/SCNS/NCEMA 7000:2021',
                'version' => 'V1.0',
                'controls_path' => 'database/seeders/Compliance/standards/NCEMA 7000-2021-dot.csv',
                'controls_seperator' => '.',
            ],
            [
                'name' => 'UAE IA',
                'version' => 'V1.0',
                'controls_path' => 'database/seeders/Compliance/standards/UAE_IA-_DOT.csv',
                'controls_seperator' => '.',
            ]
        ];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->syncStandardControls();

        $this->syncProjectControls();
        $this->info('Done.');

        return 0;
    }

    private function syncStandardControls()
    {
        // if ($this->confirm('Do you want to backup the db?')) {
        //     exec(sprintf("mysqldump --user %s --password %s %s > backup.sql", env('DB_USERNAME'), env('DB_PASSWORD'), ENV('DB_DATABASE')));
        //     $this->newLine();
        // }

        $defaultStandards = $this->getStandards();

        $this->info(sprintf('Loaded %d standards.', count($defaultStandards)));
        $this->newLine();

        $standards_missing = false;
        foreach ($defaultStandards as $standard) {
            $record = Standard::query()->with('controls')->where('name', $standard['name'])->where('version', $standard['version'])->first();
            if ($record) {

                $seeded_controls_count = $record->controls->count();

                $this->info(sprintf("Standard: '%s' has %d controls seeded.", $record->name, $seeded_controls_count));

                $rows = Excel::toCollection(collect([]), base_path($standard['controls_path']))->first();

                // remove table heading
                $rows->shift();
                $rows = $rows->toArray();
                $controls_count = count($rows);

                if ($seeded_controls_count < $controls_count) {

                    $this->newLine();
                    $this->warn('Some controls are missing, checking ...');
                    $this->newLine();

                    $bar = $this->output->createProgressBar($controls_count);

                    $missing = $controls_count - $seeded_controls_count;
                    $added = 0;

                    DB::beginTransaction();
                    foreach ($rows as $row) {

                        if (!$record->controls()
                            ->where('primary_id', ($row[0]))
                            ->where('sub_id', ($row[1]))
                            ->whereIn('name', [htmlspecialchars($row[2], ENT_QUOTES, 'utf-8'), clean($row[2]), $row[2]])
                            ->whereIn('description', [clean($row[3]), $row[3]])
                            ->exists()) {
                            $control = StandardControl::create([
                                'standard_id' => $record->id,
                                'primary_id' => $row[0],
                                'sub_id' => $row[1],
                                'id_separator' => $standard['controls_seperator'],
                                'name' => $row[2],
                                'slug' => Str::slug($row[2]),
                                'description' => $row[3],
                            ]);
                            if ($control) $added++;
                            $bar->advance();
                        }
                    }
                    $bar->finish();

                    $this->newLine();
                    $this->newLine();

                    if ($added !== $missing) {
                        $this->warn('Something went wrong, rolling back ...');
                        DB::rollBack();
                    } else {
                        $this->warn(sprintf('Total: %d | Got: %d | Missing: %d | Added: %d', $controls_count, $seeded_controls_count, $missing, $added));
                        DB::commit();
                    }
                }
            } else {
                $standards_missing = true;
                $this->warn(sprintf("Standard: '%s', doesn't exist!", $standard['name']));
            }

            $this->newLine();
        }
        if ($standards_missing && $this->confirm("Some standards are missing, do you want to run db:seed ?")) {
            Artisan::call("db:seed");
            $this->info(Artisan::output());
        }
    }

    private function syncProjectControls()
    {
        // if (!$this->confirm('Do you want to sync projects with the new controls ?')) {
        //     return;
        // }

        $projects = Project::whereHas('of_standard', function ($q) {
            $q->where('is_default', 1);
        })->with('controls')->get();

        $bar = $this->output->createProgressBar($projects->count());
        if ($projects->count() > 0) {
            $bar->start();
        }

        foreach ($projects as $project) {
            $standard_controls = StandardControl::where('standard_id', $project->standard_id)->get();
            $project_controls = $project->controls;

            $missing_controls_count = count($standard_controls) - count($project_controls);
            $added_project_controls = 0;
            if ($missing_controls_count > 0) {

                $missing_keys = [];
                $clean_project_controls = collect($project_controls)->map(function ($control) {
                    return [$control->primary_id, $control->sub_id, $this->transformString($control->name), $this->transformString($control->description)];
                })->toArray();
                $clean_standard_controls = collect($standard_controls)->map(function ($control) {
                    return [$control->primary_id, $control->sub_id, $this->transformString($control->name), $this->transformString($control->description)];
                })->toArray();
                for ($i = 0; $i < count($clean_standard_controls); $i++) {
                    if (!in_array($clean_standard_controls[$i], $clean_project_controls)) $missing_keys[] = $i;
                }

                DB::beginTransaction();
                foreach ($missing_keys as $k) {
                    $control = $standard_controls[$k];
                    $project_control = $project->controls()->create([
                        'name' => $control->name,
                        'primary_id' => $control->primary_id,
                        'id_separator' => $control->id_separator,
                        'sub_id' => $control->sub_id,
                        'description' => $control->description
                    ]);
                    if ($project_control) {
                        $added_project_controls++;
                    }
                }

                $this->newLine();
                $this->newLine();

                $this->warn("added: $added_project_controls  missing: $missing_controls_count");
                if ($added_project_controls !== $missing_controls_count) {
                    $this->warn('Something went wrong, rolling back ...');
                    DB::rollBack();
                } else {
                    $this->warn(sprintf('Total: %d | Got: %d | Missing: %d | Added: %d', count($standard_controls), count($project_controls), $missing_controls_count, $added_project_controls));
                    DB::commit();
                }
            }
            $bar->advance();
        }

        //output messages
        if ($projects->count() > 0) {
            $bar->finish();
        }
        $this->newLine();
        $this->info('Projects were synced');
    }

    private function transformString($string)
    {
        $string = htmlspecialchars_decode($string, ENT_QUOTES);

        $string = preg_replace('/[^\p{L}\p{N}\s]/u', '', $string);

        $string = trim($string);

        $string = str_replace([" ", "\n"], "", $string);

        return $string;

    }
}
