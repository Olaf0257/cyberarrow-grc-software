<?php

namespace Database\Seeders\Administration\Settings;

use Illuminate\Database\Seeder;

class DefaultMailSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default risk templates
        \DB::table('mail_settings')->insert([
            'mail_driver' => 'smtp',
            'mail_host' => 'smtp.sendgrid.net',
            'mail_port' => '465',
            'mail_from_address' => 'grc@ebdaa.ae',
            'mail_from_name' => 'CyberArrow GRC',
            'mail_username' => 'apikey',
            'mail_password' => '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',
            'mail_encryption' => 'SSL'
        ]);
    }
}
