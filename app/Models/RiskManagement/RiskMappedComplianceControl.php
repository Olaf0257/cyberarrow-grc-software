<?php

namespace App\Models\RiskManagement;

use Illuminate\Database\Eloquent\Model;

class RiskMappedComplianceControl extends Model
{
    protected $table = 'risks_mapped_compliance_controls';

    protected $guarded = ['id'];

    public function complianceProjectControl(){
        return $this->belongsTo('App\Models\Compliance\ProjectControl', 'control_id');
    }
}
