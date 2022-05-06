<?php

namespace App\Models\RiskManagement\RiskMatrix;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskScoreLevelType extends Model
{
    use HasFactory;

    protected $table = 'risk_score_matrix_level_types';

    /**
     * Get the levels level types.
     */
    public function levels()
    {
        return $this->hasMany(RiskScoreLevel::class, 'level_type', 'level');
    }
}
