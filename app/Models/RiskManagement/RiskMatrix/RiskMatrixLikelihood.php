<?php

namespace App\Models\RiskManagement\RiskMatrix;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Mews\Purifier\Casts\CleanHtml;

class RiskMatrixLikelihood extends Model
{
    use HasFactory;

    protected $table = 'risk_score_matrix_likelihoods';
    protected $fillable = ['name', 'index'];


    protected $casts = [
        'name'    => CleanHtml::class,
    ];


    /**
     * Get the scores for the likelihood.
     */
    public function scores()
    {
        return $this->hasMany(RiskMatrixScore::class, 'likelihood_index');
    }
}
