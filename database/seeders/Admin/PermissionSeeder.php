<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = array(
            "Auditor" => array(
                'reporting-view',
                'policy-reports-view',
                'policy-reports-create',
                'policy-reports-edit',
                'policy-reports-delete',
                'policy-reports-detail'
            ),
            "Contributor" => array(
                'compliance-dashboard',
                'compliance-project-view',
                'compliance-project-create',
                'compliance-project-edit',
                'compliance-project-delete',
                'compliance-project-detail'
            ),
            "Compliance Administrator" => array(
                'compliance-dashboard',
                'compliance-project-view',
                'compliance-project-create',
                'compliance-project-edit',
                'compliance-project-delete',
                'compliance-project-detail'
            ),
            "Policy Administrator" => array(
                'policy-campaign-view',
                'policy-campaign-create',
                'policy-campaign-edit',
                'policy-campaign-delete',
                'policy-campaign-detail',
                'policy-policies-view',
                'policy-policies-create',
                'policy-policies-edit',
                'policy-policies-delete',
                'policy-policies-detail',
                'policy-users-view',
                'policy-users-create',
                'policy-users-edit',
                'policy-users-delete',
                'policy-users-detail',
                'policy-reports-view',
                'policy-reports-create',
                'policy-reports-edit',
                'policy-reports-delete',
                'policy-reports-detail'
            ),
            "Risk Administrator" => array(
                'risk-dashboard',
                'risk-register-view',
                'risk-register-create',
                'risk-register-edit',
                'risk-register-delete',
                'risk-register-detail',
                'risk-wizard-view',
                'risk-wizard-create',
                'risk-wizard-edit',
                'risk-wizard-delete',
                'risk-wizard-detail'
            )
        );
        
        $roles = Role::all();
        foreach($permissions as $key => $value) {
            foreach($roles as $role) {
                if($key == $role->name) {
                    foreach($value as $permission) {
                        $checkExist = Permission::where('name', $permission)->first();
                        if(!$checkExist) {
                            $permissionSeed = Permission::create(['guard_name' => 'admin', 'name' => $permission]);
                        }
                    }
                    $role->syncPermissions($value);
                }
            }
        }
    }
}
