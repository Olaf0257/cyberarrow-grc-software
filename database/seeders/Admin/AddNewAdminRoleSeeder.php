<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\UserManagement\Admin;

class AddNewAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $newRoles = array(
            "Third Party Risk Administrator"
        );

        foreach ($newRoles as $role) {
            $doesntExist = Role::where('name', $role)->doesntExist();
            if($doesntExist) {
                $roleSeed = Role::create(['guard_name' => 'admin', 'name' => $role]);
            }
        }
    }
}
