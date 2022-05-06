<?php

namespace Database\Seeders\ThirdPartyRisk;

use Illuminate\Http\File;
use Illuminate\Database\Seeder;
use App\Imports\QuestionsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ThirdPartyRisk\Question;
use App\Models\ThirdPartyRisk\Questionnaire;

class FixQuestionnaireSeeder extends Seeder
{
    public $third_party_risk_base_path = 'database/seeders/ThirdPartyRisk/DefaultQuestions/';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questionnaire=Questionnaire::where('name', 'Default')->first();
        $questions_count=Question::where('questionnaire_id',$questionnaire->id)->count();
        if ($questions_count==0) {
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
