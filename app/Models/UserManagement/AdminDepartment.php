<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Administration\OrganizationManagement\Department;

class AdminDepartment extends Model
{
    use HasFactory;

    protected $fillable = ['admin_id', 'organization_id', 'department_id'];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
