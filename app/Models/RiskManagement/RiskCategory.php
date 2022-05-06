<?php

namespace App\Models\RiskManagement;

use App\Models\Administration\OrganizationManagement\Department;
use App\Models\DataScope\DataScope;
use Illuminate\Database\Eloquent\Model;
use App\Models\RiskManagement\RiskRegister;

class RiskCategory extends Model
{
    protected $table = 'risks_categories';

    protected $fillable = ['name', 'order_number'];

    public function risks()
    {
        return $this->hasMany('App\Models\RiskManagement\RisksTemplate', 'category_id');
    }

    public function registerRisks()
    {
        $relation = $this->hasMany('App\Models\RiskManagement\RiskRegister', 'category_id');

        if (request('risk_category')){
            $relation->where('category_id', request('risk_category'));
        }

        if(request('risk_name_search_query')){
            $relation->where('name', 'like', '%'.request('risk_name_search_query').'%');
        }

        if(request('updated_risks_filter') && request('updated_risks_filter') == 'true' ){
            $relation->where('is_complete', 0);
        }

        if (request('risk_name_search_within_category_query')){
            $relation->where('name', 'like', '%'.request('risk_name_search_within_category_query').'%');
        }

        

        return $relation;
    }

    public function riskRegister()
    {
        return $this->hasMany(RiskRegister::class, 'category_id');
    }

    public function riskRegisterWithoutScope(){
        return $this->hasMany(RiskRegister::class, 'category_id')->withoutGlobalScope(DataScope::class)->ofDepartment();
    }
}
