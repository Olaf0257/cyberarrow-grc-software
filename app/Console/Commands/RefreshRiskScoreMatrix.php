<?php

namespace App\Console\Commands;

use App\Models\RiskManagement\RiskMatrix\RiskScoreLevel;
use Database\Seeders\RiskManagement\RiskMatrix\RiskScoreLevelsSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefreshRiskScoreMatrix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matrix:score-refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the values for the risk score matrix level 5 and 6';
    private $correct_scores = [
        //5 Levels
        [
            'name' => 'Low Risk',
            'max_score' => 3,
            'color' => '#7DE64F',
            'level_type' => 5,
        ],
        [
            'name' => 'Moderate Risk',
            'max_score' => 10,
            'color' => '#F2FF00',
            'level_type' => 5,
        ],
        [
            'name' => 'High Risk',
            'max_score' => 16,
            'color' => '#FCCF00',
            'level_type' => 5,
        ],
        [
            'name' => 'Extreme Risk',
            'max_score' => 24,
            'color' => '#FF0000',
            'level_type' => 5,
        ],
        [
            'name' => 'Super Extreme risk',
            'max_score' => null,
            'color' => '#9B0000',
            'level_type' => 5,
        ],

        //6 Levels
        [
            'name' => 'Very Low Risk',
            'max_score' => 3,
            'color' => '#00FFFF',
            'level_type' => 6,
        ],
        [
            'name' => 'Low Risk',
            'max_score' => 10,
            'color' => '#00FF25',
            'level_type' => 6,
        ],
        [
            'name' => 'Moderate Risk',
            'max_score' => 16,
            'color' => '#F2FF00',
            'level_type' => 6,
        ],
        [
            'name' => 'High Risk',
            'max_score' => 23,
            'color' => '#FFA000',
            'level_type' => 6,
        ],
        [
            'name' => 'Very High Risk',
            'max_score' => 24,
            'color' => '#FF0000',
            'level_type' => 6,
        ],
        [
            'name' => 'Extremely High Risk',
            'max_score' => null,
            'color' => '#9B0000',
            'level_type' => 6
        ],
    ];
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * Will delete all the columns for level 6 present in DB.
     * Will insert the correct values.
     * @return int
     */
    public function handle()
    {
        Log::info('Server administrator is attempting to reset the values for risk score matrix levels 5 and 6');
        try {
            RiskScoreLevel::whereIn('level_type', [5,6])->delete();
            \DB::table('risk_score_matrix_levels')->insert(
                $this->correct_scores
            );
            $this->info('Updated risk score matrix');
            Log::info("The values for level 5 and 6 of risk score matrix were updated");
        } catch (\Throwable $e) {
            $error_message = $e->getMessage();
            $this->error("Error message: $error_message");
            $this->newLine();
            $this->warn("Updating the values for level 5 and 6 of risk score matrix failed");
            Log::info("Updating the values for level 5 and 6 of risk score matrix failed");
        }
        return 0;
    }
}
