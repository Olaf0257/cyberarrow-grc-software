<?php

namespace Database\Seeders\ThirdPartyRisk;

use App\Imports\QuestionsImport;
use App\Models\Administration\OrganizationManagement\Organization;
use App\Models\DataScope\Scopable;
use App\Models\ThirdPartyRisk\Questionnaire;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Maatwebsite\Excel\Facades\Excel;

class DefaultQuestionnaireSeeder extends Seeder
{
    public $third_party_risk_base_path = 'database/seeders/ThirdPartyRisk/DefaultQuestions/';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Questionnaire::where('name', 'Default')->where('is_default', 1)->doesntExist()) {
            $questionnaire = Questionnaire::create([
                'name' => "Default",
                'version' => "v.1.0",
                'is_default' => 1
            ]);

            // Add scope to all existing organizations/departments
            $organization = Organization::with('departments')->first();
            if ($organization) {
                $departments = $organization->departments;
                Scopable::create([
                    'organization_id' => $organization->id,
                    'scopable_id' => $questionnaire->id,
                    'scopable_type' => get_class($questionnaire),
                ]);

                if (count($departments) > 0) {
                    foreach ($departments as $department) {
                        Scopable::create([
                            'organization_id' => $organization->id,
                            'department_id' => $department->id,
                            'scopable_id' => $questionnaire->id,
                            'scopable_type' => get_class($questionnaire),
                        ]);
                    }
                }
            }

            $file_path = $this->third_party_risk_base_path . 'Default Questions v.1.0.csv';
            $questions_csv_file = new File($file_path);
            $file_data = file_get_contents($questions_csv_file);

            if (!mb_check_encoding($file_data, 'UTF-8')) {
                $utf8_file_data = utf8_encode($file_data);
                file_put_contents($questions_csv_file, $utf8_file_data);
            }

            $import = new QuestionsImport($questionnaire->id);

            Excel::import($import, $questions_csv_file);
        }
    }
}
