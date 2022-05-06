<?php

namespace Database\Seeders\Administration\Settings;

use Illuminate\Database\Seeder;

class RevertDefaultMailSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('mail_settings')->delete();
    }
}
