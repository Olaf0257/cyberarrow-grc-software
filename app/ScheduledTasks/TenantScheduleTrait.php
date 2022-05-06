<?php

namespace App\ScheduledTasks;
use Carbon\Carbon;
use App\Nova\Model\Tenant;
use Illuminate\Support\Facades\Storage;


trait TenantScheduleTrait {

    /**
     * Setup tenat content for mail
     * @param tenant_id
     */
    public function SetUpTenantMailContent($tenant_id) {
        $tenant=Tenant::where('id',$tenant_id)->first();
        $domain=$tenant->domains()->first();
        if(env('APP_ENV')=="development" || env('APP_ENV')=="production"){
            \URL::forceRootUrl('https://'.$domain->domain);
        }
        else{
            \URL::forceRootUrl('http://'.$domain->domain);
        }

        \Config::set('database.connections.mysql.database', 'tenant'.$tenant_id);
        \DB::purge('mysql');
        $globalSettings = \DB::table('account_global_settings')->first();

        if(config('filesystems.default') != 'local'){
            if($globalSettings->company_logo!='assets/images/ebdaa-Logo.png'){
                $disk = Storage::disk('s3');
                $globalSettings->company_logo = $disk->getAwsTemporaryUrl($disk->getDriver()->getAdapter(), 'public'.$globalSettings->company_logo, Carbon::now()->addMinutes(25), []);
                // return Storage::disk('s3')->temporaryUrl( 'public/global_settings/1/sBJgCIzsFNLbwdbFGHK4IOOycWyaoJziRsB6vnaE.png', now()->addMinutes(10) );
            }
        }
      
        \View::share('globalSetting', $globalSettings);


        if (\Schema::hasTable('mail_settings')) {
            $mailSettings = \DB::table('mail_settings')->first();

            if ($mailSettings && isset($mailSettings->mail_host) && isset($mailSettings->mail_username) && isset($mailSettings->mail_password) && isset($mailSettings->mail_encryption)) { //checking if table is not empty
                $config = [
                    'driver' => $mailSettings->mail_driver ?: env('MAIL_DRIVER'),
                    'host' => $mailSettings->mail_host,
                    'port' => $mailSettings->mail_port ?: env('MAIL_PORT'),
                    'from' => [
                        'address' => $mailSettings->mail_from_address ?: env('MAIL_FROM_ADDRESS', 'Ebdaa@example.com'),
                        'name' => $mailSettings->mail_from_name ?: env('MAIL_FROM_NAME', 'Ebdaa GRC'),
                    ],
                    'encryption' => $mailSettings->mail_encryption,
                    'username' => $mailSettings->mail_username,
                    'password' => $mailSettings->mail_password,
                    'markdown' => [
                        'default' => 'markdown',
                        'paths' => [resource_path('views/vendor/mail')],
                    ],
                ];

                \Config::set('mail', $config);
                (new \Illuminate\Mail\MailServiceProvider(app()))->register();
            }
        }

        \Config::set('database.connections.mysql.database', env('DB_DATABASE'));
        \DB::purge('mysql');
    }
}