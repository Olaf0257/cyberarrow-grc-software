<?php

namespace Database\Seeders\ThirdPartyRisk;

use App\Models\ThirdPartyRisk\Questionnaire;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;

class FixQuestionTypos extends Seeder
{
    public $third_party_risk_base_path = 'database/seeders/ThirdPartyRisk/DefaultQuestions/';

    /**
     * Run the database seeds.
     *
     * @return false
     */
    public function run()
    {
        $questionnaire = Questionnaire::where('is_default', 1)->with('questions')->first();
        $file_path = $this->third_party_risk_base_path . 'Fixed default questions.csv';
        $questions_csv_file = new File($file_path);
        $file_data = file_get_contents($questions_csv_file);

        if (!mb_check_encoding($file_data, 'UTF-8')) {
            $utf8_file_data = utf8_encode($file_data);
            file_put_contents($questions_csv_file, $utf8_file_data);
        }

        try {
            $file = fopen($questions_csv_file, 'r');

            $csv_data = [];
            while (($line = fgetcsv($file)) !== FALSE) {
                //$line is an array of the csv elements
                $csv_data[] = trim($line[0]);
            }
            fclose($file);

            $count = 0;

            if ($csv_data) {
                foreach ($questionnaire->questions as $key => $question) {
                    $csv_question_text =  str_replace("\n", "\r\n", $csv_data[$key]);
                    if ($question->text != $csv_question_text) {
                        $count++;
                        $question->text = $csv_data[$key];
                        $question->save();
                    }
                }
            }

        } catch (\Exception $e) {
            $this->command->error('Could not open file! Please reseed');
        }
    }
}
