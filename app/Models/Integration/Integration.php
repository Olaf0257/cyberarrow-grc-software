<?php

namespace App\Models\Integration;

use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    protected $table = 'integrations';

    protected $appends = ['logo_link'];

    public function getLogoLinkAttribute()
    {
        return asset('assets/images/integrations/'.$this->logo);
    }
}
