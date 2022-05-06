<?php

namespace App\Http\Middleware\License;

use Closure;
use session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\LicenseBox\LicenseBoxExternalAPI;


class CheckLicenseActivation
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle($request, Closure $next) {

        try{
          
            //Check If license File Exist
           
                // fetch From Db
            $license = new LicenseBoxExternalAPI();
         
            // Checking License File exist or no
            if($license->check_local_license_exist())
            {
                // If exist than verify
                $verifyLicense = $license->verify_license();

                if($verifyLicense['status'])
                {
                    return $next($request); 
                }

                session(['licenseMessage' => $verifyLicense]);
                
                /* redirect to contact support page in case of expired licenses*/

                if (!$request->expectsJson()) {
                    return redirect()->route('license.contact.support');
                }

                return response()->json(['exception' => 'licenseException.'], 401);

                  /* Redirect to activation page  */
               

                
            }
            
          
            if($request->ajax())
            {
                return response()->json(['exception' => 'licenseException.'], 401);
              
            }

            return redirect()->route('license.activate');

        }catch(\Exception $e){
       
            \Log::error($e);
        }
    }
}
