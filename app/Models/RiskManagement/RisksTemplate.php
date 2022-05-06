<?php

namespace App\Models\RiskManagement;

use Illuminate\Database\Eloquent\Model;

class RisksTemplate extends Model
{
    protected $table = 'risks_template';

    public function category()
    {
        return $this->belongsTo('App\Models\RiskManagement\RiskCategory', 'category_id');
    }

    public function standard()
    {
        return $this->belongsTo('App\Models\RiskManagement\RiskStandard', 'standard_id');
    }
}
