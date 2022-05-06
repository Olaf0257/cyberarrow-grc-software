<?php

namespace Database\Seeders\Admin;

use App\Models\Administration\OrganizationManagement\Organization;
use App\Models\UserManagement\Admin;
use App\Models\UserManagement\AdminDepartment;
use Illuminate\Database\Seeder;

class DefaultOrganization extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $organization = Organization::first();

        if (!$organization) {
            $organization = Organization::Create(array(
                'name' => 'Cyberarrow'
            ));
        }

        $admin = Admin::where('email', 'admin@admin.com')->first();

        if (!$admin->department) {
            $department = new AdminDepartment(['organization_id' => $organization->id]);
            $admin->department()->save($department);
        }
    }
}
