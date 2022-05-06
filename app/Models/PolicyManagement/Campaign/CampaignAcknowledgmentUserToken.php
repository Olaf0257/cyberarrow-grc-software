<?php

namespace App\Models\PolicyManagement\Campaign;

use Illuminate\Database\Eloquent\Model;

class CampaignAcknowledgmentUserToken extends Model
{
    protected $table = 'policy_campaign_acknowledgment_user_tokens';
    protected $fillable = ['campaign_id', 'user_id', 'token'];

    /***
     * get  user
     */
    public function user()
    {
        return $this->belongsTo('App\Models\PolicyManagement\Campaign\CampaignGroupUser', 'user_id', 'id');
    }

    /***
     * get  campaign
     */
    public function campaign()
    {
        return $this->belongsTo('App\Models\PolicyManagement\Campaign\Campaign', 'campaign_id', 'id');
    }
}
