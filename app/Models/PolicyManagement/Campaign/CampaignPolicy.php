<?php

namespace App\Models\PolicyManagement\Campaign;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CampaignPolicy extends Model
{
    protected $table = 'policy_campaign_policies';
    protected $fillable = ['policy_id', 'display_name', 'type', 'path', 'version', 'description'];
    protected $appends = ['ext'];

    public function getExtAttribute()
    {
        if ($this->type == "doculink") {
            return "";
        }

        $ext = pathinfo(parse_url(storage_path($this->path))['path'], PATHINFO_EXTENSION);

        return $ext;
    }

    public function getPathAttribute($value){
        if(config('filesystems.default') == 's3'){
            if($this->type=='doculink'){
                return $value;
            }
            else{
                $disk = Storage::disk('s3');
                $url = $disk->getAwsTemporaryUrl($disk->getDriver()->getAdapter(), 'public/'.$value, Carbon::now()->addMinutes(5), []);
                return $url;
            }
        }
        else{
            return $value;
        }
       
    }
}
