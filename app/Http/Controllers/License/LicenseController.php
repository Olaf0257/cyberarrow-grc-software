<?php

namespace App\Http\Controllers\License;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\LicenseBox\LicenseBoxExternalAPI;

class LicenseController extends Controller
{


    protected $license;

    public function __construct(LicenseBoxExternalAPI $licenseBox)
    {

        $this->license = $licenseBox;

    }

    public function activationPage()
    {
       $licFile = Storage::path('private/license/.lic');
       $logBtn = false;

       if(is_file($licFile))
       {
            $logBtn = true;
       } 

       return view('license.register',compact('logBtn'));
    }


    public function contactSupport()
    {

       $res = session()->get('licenseMessage');
     
       if(!$res)
       {    
            $res['message'] = '';
       }
       
        $data['title'] = 'Error';
        $data['status'] = isset($res['status']) ?: 'false';
        $data['message'] = $res['message'];
        $data['action'] = true;


        if($this->license->check_local_license_exist())
        {
            $data['redirect'] = url('/');
        }

        return view('license.contact',compact('data'));

    }


    public function activateLicense(Request $request)
    {
          // Validate Data
            $this->validate($request,[
                'license' => 'required',
                'client'  => 'required'
            ]);

             //    Checking connection and call the license activate method
            $checkConnection = $this->license->check_connection();

            if(isset($checkConnection['status']) && $checkConnection['status'])
            {
                $activateLicense = $this->license->activate_license($request->license, $request->client);

                //    Redirect If Status is true
                if($activateLicense['status'])
                {
                    $data['title'] = 'License Activated';
                    $data['status'] = $activateLicense['status'];
                    $data['message'] = $activateLicense['message'];
                    $data['action'] = true;

                    // Getting config file of license
                    $conFile = Config::get('license.license');
                 
                    // Check If Empty Client name
                    if($conFile['client_name'] == '') 
                    {
                        // If YEs Set Client Name
                        $conFile['client_name'] = trim($request->client);
                        $setClientName = var_export($conFile,1);

                        File::put(base_path() . '/config/license/license.php', "<?php\n return $setClientName ;");
                    }

                }else
                {
                    $data['title'] = 'Error';
                    $data['status'] = $activateLicense['status'];
                    $data['message'] = $activateLicense['message'];
                    $data['action'] = true;
                    $data['btnTxt'] = 'Try Again';
                }

                return view('pages.messages',compact('data'));
            }

            $data['status'] = $checkConnection['status'];
            $data['message'] = $checkConnection['message'];

            return $this->contactSupport($data);

    }

    // Hit Update Api for available updates and Return Response
    public function checkForUpdates()
    {
        $activateLicense = $this->license->check_update();
      
        return $activateLicense;
     
    }

    // Download New Updates From Api
    public function downloadUpdate()
    {
        try{
              
            // checking for updates before downloading
            $latestUpdate = $this->checkForUpdates();
            
            $updateId = $latestUpdate['update_id'];
            $has_sql = $latestUpdate['has_sql'];
            $version = $latestUpdate['version'];
            $dbConfig = [];

            // Checking if downloaded file has sql
            if($has_sql)
            {
                // Get Details if sql is available
                // $getDbDetails = Config::get('database.connections');
                
                $dbConfig = [
                    'db_host' => env('DB_HOST'),
                    'db_name' => env('DB_DATABASE'),
                    'db_pass' => env("DB_PASSWORD"),
                    'db_port' => env('DB_PORT'),
                    'db_user' => env("DB_USERNAME"),
                ];
            }
      
            //Down Command for making app in maintenance mode
            Artisan::call('down');

            // Download Update From LicenseBox
            $this->license->download_update($updateId,$has_sql,$version,null,null,$dbConfig);

            // Clear all cache and migrate table if available
            // Executing command for composer update
            // app()->make(\App\LicenseBox\Composer::class)->run(['dump-autoload']);
            $shellCmd = 'cd ' . base_path() . ' && composer install && php artisan optimize:clear && php artisan migrate && php artisan db:seed';
            shell_exec($shellCmd);

            // app()->make(\App\LicenseBox\Composer::class)->run(['install']);
            session()->put('updated',true);
            Artisan::call('up');
            
        }catch(\Exception $e)
        {
            \Log::error($e);
        }
      
    }
    

}
