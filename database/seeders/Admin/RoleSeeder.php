<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\UserManagement\Admin;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = array(
            "Global Admin",
            "Auditor",
            "Contributor",
            "Compliance Administrator",
            "Policy Administrator",
            "Risk Administrator",
            "Third Party Risk Administrator"
        );
        
        foreach($roles as $role) {
            $roleSeed = Role::create(['guard_name' => 'admin', 'name' => $role]);
            if($role == "Global Admin") {
                $admin = Admin::first();
                $admin->assignRole([$roleSeed->id]);
            }
        }
    }
}
