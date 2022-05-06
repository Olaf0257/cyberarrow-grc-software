<?php

namespace App\Models\RiskManagement\RiskMatrix;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskMatrixAcceptableScore extends Model
{
    use HasFactory;
    protected $table = 'risk_acceptable_score';
    protected $fillable = ['score'];
}
