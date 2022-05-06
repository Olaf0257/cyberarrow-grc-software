<?php

namespace App\Models\RiskManagement\RiskMatrix;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskMatrixScore extends Model
{
    use HasFactory;

    protected $table = 'risk_score_matrix_scores';
    protected $fillable = ['score', 'likelihood_index', 'impact_index'];
}
