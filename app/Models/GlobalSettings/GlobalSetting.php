<?php

namespace App\Models\GlobalSettings;

use Carbon\Carbon;
use \Mews\Purifier\Casts\CleanHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GlobalSetting extends Model
{

    protected $table = 'account_global_settings';
    protected $guarded = ['id'];


    protected $casts = [
        'display_name'    => CleanHtml::class,
        'primary_color'    => CleanHtml::class,
        'secondary_color'    => CleanHtml::class,
        'default_text_color'    => CleanHtml::class,
    ];

    public function getCompanyLogoAttribute($value)
    {
        if(config('filesystems.default') == 's3'){
        
            if($value=='assets/images/ebdaa-Logo.png'){
                $url= $value;
            }
            else{
                $disk = Storage::disk('s3');
                $url = $disk->getAwsTemporaryUrl($disk->getDriver()->getAdapter(), 'public'.$value, Carbon::now()->addMinutes(5), []);
                // return Storage::disk('s3')->temporaryUrl( 'public/global_settings/1/sBJgCIzsFNLbwdbFGHK4IOOycWyaoJziRsB6vnaE.png', now()->addMinutes(10) );
            }
            return $url;
        }
        else{
            return $value;
        }
    }

    public function getFaviconAttribute($value)
    {
        if(config('filesystems.default') == 's3'){
            if($value=='assets/images/ebdaa-Logo.png'){
                $url= $value;
            }
            else{
                $disk = Storage::disk('s3');
                $url = $disk->getAwsTemporaryUrl($disk->getDriver()->getAdapter(), 'public'.$value, Carbon::now()->addMinutes(5), []);
                // return Storage::disk('s3')->temporaryUrl( 'public/global_settings/1/sBJgCIzsFNLbwdbFGHK4IOOycWyaoJziRsB6vnaE.png', now()->addMinutes(10) );
            }
            return $url;
        }
        else{
            return $value;
        }
    }

}
