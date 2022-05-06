<?php

namespace App\Models\Integration;

use Illuminate\Database\Eloquent\Model;

class IntegrationCategory extends Model
{
    protected $table = 'integration_categories';

    const BUSINESS_SUIT_ID = 1;
    const SSO_ID = 2;
    const CLOUD_SERVICES_ID = 3;
    const DEVELOPMENT_TOOLS_ID = 4;
    const TICKETING_ID = 5;
    const DEVICE_MANAGEMENT_ID = 6;
    const ASSET_MANAGEMENT_AND_HELPDESK_ID = 7;
    const SDLC_ID = 8;

    public function integrations()
    {
        return $this->hasMany(Integration::class,'category_id');
    }
}
