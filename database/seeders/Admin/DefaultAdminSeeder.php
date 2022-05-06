<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use App\Models\UserManagement\Admin;

class DefaultAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Admin::where('email', 'admin@admin.com')->first();

        if (!$admin) {
            Admin::Create(array(
                'first_name' => 'Admin',
                'last_name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('admin'),
                'contact_number_country_code' => 'ae',
                'contact_number' => '45566555',
                'status' => 'active',
            ));
        }
    }
}
