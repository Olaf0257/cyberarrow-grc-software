<?php

namespace App\Models\DataScope;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Administration\OrganizationManagement\Department;

class DataScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if(request('data_scope')){
            $dataScope = explode('-', request('data_scope'));
            $organizationId = $dataScope[0];
            $departmentId = $dataScope[1];

            /* data scoping query*/
            $builder->whereHas('scope', function ($query) use ($departmentId, $organizationId) {
                $query->where('organization_id',$organizationId);

                if($departmentId > 0){
                    $query->where('department_id',$departmentId);
                } else {
                    $query->whereNull('department_id');
                }
            });

        }
    }
}
