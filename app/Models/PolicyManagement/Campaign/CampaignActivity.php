<?php

namespace App\Models\PolicyManagement\Campaign;

use Illuminate\Database\Eloquent\Model;

class CampaignActivity extends Model
{
    protected $table = 'policy_campaign_activities';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['activity', 'campaign_id', 'type', 'user_id'];

    /***
     * get activity user
     */
    public function user()
    {
        return $this->belongsTo('App\Models\PolicyManagement\Campaign\CampaignGroupUser', 'user_id', 'id');
    }

    /***
     * get activity campaign
     */
    public function campaign()
    {
        return $this->belongsTo('App\Models\PolicyManagement\Campaign\Campaign', 'campaign_id', 'id');
    }
}
