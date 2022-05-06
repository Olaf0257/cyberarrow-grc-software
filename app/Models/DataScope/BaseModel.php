<?php

namespace App\Models\DataScope;

use App\Models\DataScope\Scopable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Auth;

class BaseModel extends Model
{
    use HasFactory;
    
     /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // while retreiving list add department and organization scope
        static::addGlobalScope(new DataScope);

        /**
         * while creating create morphedByMany Manually
         * eg. $this->morphedByMany(RiskRegister::class, 'scopable')->where('department_id',$this->id);
         */
        self::created(function($model){
            if(request('data_scope')){
                $dataScope = explode('-', request('data_scope'));
                $organizationId = $dataScope[0];
                $departmentId = $dataScope[1];
                if($departmentId > 0){
                    Scopable::create([
                        'organization_id' => $organizationId,
                        'department_id'=> $departmentId,
                        'scopable_id'=>$model->id,
                        'scopable_type'=>get_class($model)
                    ]);
                }
                else{
                    Scopable::create([
                        'organization_id' => $organizationId,
                        'scopable_id'=>$model->id,
                        'scopable_type'=>get_class($model)
                    ]);
                }
            }
        });
    }

    /**
     * retreive scope data ( department_id , organaization_id )
     * used when retreiving data, check scope class App\Models\DataScope\DataScope
     */
    public function scope()
    {
        return $this->hasOne(Scopable::class,'scopable_id')->where('scopable_type',get_class($this));
    }

    /**
     * tree select department data (own department and department array passed )
     */
    public function scopeOfDepartment($query)
    {
        $authUser=Auth::guard('admin')->user();
        $dataScope = explode('-', request('data_scope'));
        $departmentId = $dataScope[1];
        if($authUser->hasRole('Global Admin')){
           
            if($departmentId){
                $all_departments=array_merge($_REQUEST['departments'], [$departmentId]);
                $query->whereHas('scope', function ($qur) use ($all_departments) {
                    $qur->whereIn('department_id',$all_departments);
                });
            }
            else{
                $query->whereHas('scope', function ($qur) {
                    $qur->whereNull('department_id')
                            ->orWhereIn('department_id',$_REQUEST['departments']);
                });
            }
            
        }
        else{
            $all_departments=array_merge($_REQUEST['departments'], [$departmentId]);
            // if scope is the organization one then include null deparment scopes
            if(request('data_scope')==='1-0'){
                $query->whereHas('scope', function ($qur) use ($all_departments) {
                    $qur->whereNull('department_id')
                         ->orWhereIn('department_id',$all_departments);
                });
            }
            else{
                $query->whereHas('scope', function ($qur) use ($all_departments) {
                    $qur->whereIn('department_id',$all_departments);
                });
            }
            
        }
    }

}
