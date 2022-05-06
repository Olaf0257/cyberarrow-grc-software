<?php

namespace App\Models\Administration\OrganizationManagement;

use App\Models\UserManagement\Admin;
use Database\Factories\DepartmentFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\RiskManagement\RiskRegister;
use App\Models\UserManagement\AdminDepartment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \Mews\Purifier\Casts\CleanHtml;

class Department extends Model
{
    use HasFactory;
    protected $table = 'departments';
    protected $fillable = ['organization_id', 'name', 'parent_id', 'sort_order'];

    protected $with = ['departments'];

    protected $casts = [
        'name'    => CleanHtml::class,

    ];

    public function departments()
    {
        return $this->hasMany(Department::class, 'parent_id', 'id')->orderBy('sort_order', 'ASC');
    }

    public function users()
    {
        return $this->hasManyThrough(Admin::class, AdminDepartment::class, 'department_id', 'id');
    }

    public function getAllChildDepartIds($department = null,  $allDepart = [])
    {
        $department = $department ?: $this;

       if ($department->departments) {
           foreach ($department->departments as $depart) {
            $allDepart[] = $depart->id;

             if ($depart->departments) {
                $allDepart = $this->getAllChildDepartIds($depart, $allDepart);
             }
           }
       }

        return $allDepart;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return DepartmentFactory::new();
    }
}
