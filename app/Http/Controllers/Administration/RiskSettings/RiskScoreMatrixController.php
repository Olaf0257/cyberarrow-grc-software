<?php

namespace App\Http\Controllers\Administration\RiskSettings;

use App\Http\Controllers\Controller;
use App\Models\RiskManagement\RiskRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixLikelihood;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixImpact;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixScore;
use App\Models\RiskManagement\RiskMatrix\RiskScoreLevel;
use App\Models\RiskManagement\RiskMatrix\RiskScoreLevelType;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixAcceptableScore;
use Database\Seeders\RiskManagement\RiskMatrix\ImpactDefaultSeeder;
use Database\Seeders\RiskManagement\RiskMatrix\LikelihoodDefaultSeeder;
use Database\Seeders\RiskManagement\RiskMatrix\ScoreDefaultSeeder;
use Database\Seeders\RiskManagement\RiskMatrix\RiskScoreLevelTypesSeeder;
use Database\Seeders\RiskManagement\RiskMatrix\RiskScoreLevelsSeeder;
use Database\Seeders\RiskManagement\RiskMatrix\RiskAcceptableScoreSeeder;
use Illuminate\Support\Facades\Log;


class RiskScoreMatrixController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'risk_acceptable_score' => 'required'
        ]);
        $riskMatrixData = json_decode($request->risk_matrix_data, FALSE);

        $riskMatrix = $riskMatrixData->matrix;
        $riskLevels = $riskMatrixData->levels;

        $likelihoodInputs = $riskMatrix->likelihoods;
        $impactInputs = $riskMatrix->impacts;
        $scoreRawInputs = $riskMatrix->riskScores;

        /* Updating risk likelihood*/
        DB::transaction(function () use ($likelihoodInputs, $impactInputs, $request, $scoreRawInputs, $riskLevels) {
            $updatedRiskMatrixLikelihoods = [];
            $updatedRiskMatrixImpacts = [];
            $updatedRiskMatrixScores = [];

            /* Creating or Updating likelihoods */
            foreach ($likelihoodInputs as $key => $likelihoodInput) {
                $likelihood = RiskMatrixLikelihood::where('index', $key)->first();

                /* Updating model */
                if ($likelihood) {
                    $likelihood->name = $likelihoodInput->name;

                    $likelihood->update();

                    $updatedRiskMatrixLikelihoods[] = $likelihood;
                    /* Continue to next iteration */
                    continue;
                }


                /* creating new likelihood when  not exist*/
                $newRiskLikelihood = RiskMatrixLikelihood::Create([
                    'name' => $likelihoodInput->name,
                    'index' => $key
                ]);

                $updatedRiskMatrixLikelihoods[] = $newRiskLikelihood;
            }


            /* Creating or Updating likelihoods */
            foreach ($impactInputs as $key => $impactInput) {
                $impact = RiskMatrixImpact::where('index', $key)->first();


                if ($impact) {
                    $impact->name = $impactInput->name;

                    $impact->update();

                    $updatedRiskMatrixImpacts[] = $impact;

                    /* Continue to next iteration */
                    continue;
                }


                /* creating new likelihood when  not exist*/
                $newRiskImpact = RiskMatrixImpact::Create([
                    'name' => $impactInput->name,
                    'index' => $key
                ]);


                $updatedRiskMatrixImpacts[] = $newRiskImpact;
            }

            /* Creating or Updating risk scores */
            foreach ($scoreRawInputs as $key => $scoreInputs) {
                $riskLikelihood = $updatedRiskMatrixLikelihoods[$key];


                foreach ($scoreInputs as $key => $scoreInput) {
                    $riskImpact = $updatedRiskMatrixImpacts[$key];

                    if ($scoreInput->id) {
                        $riskScore = RiskMatrixScore::find($scoreInput->id);


                        if ($riskScore) {
                            $riskScore->score = $scoreInput->score;
                            $riskScore->likelihood_index = $riskLikelihood->index;
                            $riskScore->impact_index = $riskImpact->index;

                            $riskScore->update();

                            $updatedRiskMatrixScores[] = $riskScore;
                        }

                        /* Continue to next iteration */
                        continue;
                    }


                    /* creating new likelihood when  not exist*/
                    $newRiskScore = RiskMatrixScore::Create([
                        'score' => $scoreInput->score,
                        'likelihood_index' => $riskLikelihood->index,
                        'impact_index' => $riskImpact->index
                    ]);

                    $updatedRiskMatrixScores[] = $newRiskScore;
                }
            }

            /* Extracting items not to be deleted */

            $notToBeDeletedRiskLikelihoodIds = collect($updatedRiskMatrixLikelihoods)->pluck(['id'])->toArray();
            $notToBeDeletedRiskImpactIds = collect($updatedRiskMatrixImpacts)->pluck(['id'])->toArray();
            $notToBeDeletedRiskScoreIds = collect($updatedRiskMatrixScores)->pluck(['id'])->toArray();

            /* Deleting Likelihoods */
            RiskMatrixLikelihood::whereNotIn('id', $notToBeDeletedRiskLikelihoodIds)->delete();

            /* Deleting impacts */
            RiskMatrixImpact::whereNotIn('id', $notToBeDeletedRiskImpactIds)->delete();

            /* Deleting scores */
            RiskMatrixScore::whereNotIn('id', $notToBeDeletedRiskScoreIds)->delete();


            /* Risk levels update operations*/
            if (count($riskLevels) > 0) {
                $newRiskLevelType = $riskLevels[0]->level_type;
                $newRiskLevelTypeModel = RiskScoreLevelType::where('level', $newRiskLevelType)->where('is_active', 0)->first();

                if ($newRiskLevelTypeModel) {

                    /*Making the previous active level non active*/
                    $prevActiveLevelType = RiskScoreLevelType::where('is_active', 1)->first();
                    $prevActiveLevelType->is_active = 0;
                    $prevActiveLevelType->update();

                    /* making new level type active */
                    $newRiskLevelTypeModel->is_active = 1;
                    $newRiskLevelTypeModel->update();
                }

                foreach ($riskLevels as $key => $riskLevel) {
                    $riskLevelModel = RiskScoreLevel::where('id', $riskLevel->id)->first();

                    if ($riskLevelModel) {
                        $riskLevelModel->name = $riskLevel->name;
                        $riskLevelModel->max_score = $riskLevel->max_score;
                        $riskLevelModel->update();
                    }
                }
            }


            /* Storing risk acceptable score */
            $riskAcceptableScore = RiskMatrixAcceptableScore::first();

            /* update when already exist or create new when not found */
            if ($riskAcceptableScore) {
                $riskAcceptableScore->update([
                    'score' => $request->risk_acceptable_score
                ]);
            } else {
                RiskMatrixAcceptableScore::Create([
                    'score' => $request->risk_acceptable_score
                ]);
            }
        }, 5);
        Log::info('User has updated risk score matrix');


        // update the risks
        $lastLikelihood = RiskMatrixLikelihood::orderBy('id', 'desc')->first();
        $lastImpact = RiskMatrixImpact::orderBy('id', 'desc')->first();
        $riskAcceptableScore = RiskMatrixAcceptableScore::first();

        $modified_likelihood = $lastLikelihood->index + 1;
        $modified_impact = $lastImpact->index + 1;

        RiskRegister::query()
            ->where('impact', '>', $modified_impact)
            ->orWhere('likelihood', '>', $modified_likelihood)
            ->each(function ($riskRegister) use ($riskAcceptableScore, $modified_likelihood, $modified_impact) {
                $impact = $riskRegister->impact > $modified_impact ? $modified_impact : $riskRegister->impact;
                $likelihood = $riskRegister->likelihood > $modified_likelihood ? $modified_likelihood : $riskRegister->likelihood;

                $riskRegister->update([
                    'likelihood' => $likelihood,
                    'impact' => $impact
                ]);
                $riskRegister = $riskRegister->refresh();

                $riskScore = RiskMatrixScore::query()
                    ->where('likelihood_index', $riskRegister->likelihood - 1)
                    ->where('impact_index', $riskRegister->impact - 1)
                    ->first();

                if ($riskScore) {
                    $inherent_score = $riskScore->score;
                    $residual_score = $riskScore->score;

                    if ($riskRegister->residual_score !== $riskRegister->inherent_score) {
                        $residual_score = $riskAcceptableScore->score;
                    }
                    $riskRegister->update([
                        'residual_score' => $residual_score,
                        'inherent_score' => $inherent_score
                    ]);
                }

            });
        //


        return redirect()->back()->with([
            'success' => 'Risk matrix updated successfully.',
            'activeTab' => 'riskSettings',
        ]);
    }

    //
    public function restoreRiskMatrixToDefault(Request $request)
    {

        Log::info('User is attempting to restore risk score matrix to default');
        /* truncating the all risk matrix related tables*/
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        RiskMatrixAcceptableScore::query()->truncate();
        RiskMatrixScore::query()->truncate();
        RiskMatrixLikelihood::query()->truncate();
        RiskMatrixImpact::query()->truncate();

        RiskScoreLevel::query()->truncate();
        RiskScoreLevelType::query()->truncate();
        RiskMatrixAcceptableScore::query()->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        try {
            DB::beginTransaction();
            /* seeding the risk matrix data */
            $impactDefaultSeeder = new ImpactDefaultSeeder();
            $impactDefaultSeeder->run();
            $likelihoodDefaultSeeder = new LikelihoodDefaultSeeder();
            $likelihoodDefaultSeeder->run();
            $scoreDefaultSeeder = new ScoreDefaultSeeder();
            $scoreDefaultSeeder->run();
            $riskScoreLevelTypesSeeder = new RiskScoreLevelTypesSeeder();
            $riskScoreLevelTypesSeeder->run();
            $riskScoreLevelsSeeder = new RiskScoreLevelsSeeder();
            $riskScoreLevelsSeeder->run();
            $riskAcceptableScoreSeeder = new RiskAcceptableScoreSeeder();
            $riskAcceptableScoreSeeder->run();
            Log::info('User has restored risk score matrix to default');
            DB::commit();

        } catch (\Throwable $e) {
            Log::info('Restoring risk score matrix to default failed');
            DB::rollBack();
        };

        $riskAcceptableScore = RiskMatrixAcceptableScore::first();
        RiskRegister::query()
            ->where('impact', '>', 5)
            ->orWhere('likelihood', '>', 5)
            ->each(function ($riskRegister) use ($riskAcceptableScore) {
                $likelihood = $riskRegister->likelihood > 5 ? 5 : $riskRegister->likelihood;
                $impact = $riskRegister->impact > 5 ? 5 : $riskRegister->impact;

                $riskRegister->update([
                    'impact' => $impact,
                    'likelihood' => $likelihood
                ]);

                $riskRegister = $riskRegister->refresh();

                $riskScore = RiskMatrixScore::query()
                    ->where('likelihood_index', $riskRegister->likelihood - 1)
                    ->where('impact_index', $riskRegister->impact - 1)
                    ->first();

                if ($riskScore) {
                    $inherent_score = $riskScore->score;
                    $residual_score = $riskScore->score;

                    if ($riskRegister->residual_score !== $riskRegister->inherent_score) {
                        $residual_score = $riskAcceptableScore->score;
                    }
                    $riskRegister->update([
                        'residual_score' => $residual_score,
                        'inherent_score' => $inherent_score
                    ]);
                }
            });

        return redirect()->back()->with([
            'success' => 'Risk matrix restored to default successfully.',
            'activeTab' => 'riskSettings',
        ]);
    }
}
