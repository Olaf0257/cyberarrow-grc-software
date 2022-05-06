<?php

namespace App\Traits\RisksManagement;

use App\Models\RiskManagement\RiskMatrix\RiskScoreLevel;

trait HelperMethodsTrait{

    public function getRiskLevelByScore($score)
    {
        $activeLevels = RiskScoreLevel::whereHas('levelTypes', function ($query){
            $query->where('is_active', 1);
        })->get();

        $activeLevelsLastKey = $activeLevels->keys()->last();

        foreach ($activeLevels as $index => $activeLevel) {
            $isLastKey = ($index == $activeLevelsLastKey);
            $rangeStartScore = ($index == 0) ? 1 : $activeLevels[$index-1]['max_score']+1;
            $rangeEndScore = $activeLevel->max_score;



            if ($score >= $rangeStartScore  && $score <= $rangeEndScore && !$isLastKey) {
                return $activeLevel;
                break;
            }


            /* For last level */
            if ( $score >= $rangeStartScore && $isLastKey) {
                return $activeLevel;
                break;
            }
        }


        return null;
    }
}
