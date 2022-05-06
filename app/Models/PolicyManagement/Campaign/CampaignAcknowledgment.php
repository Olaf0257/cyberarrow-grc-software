<?php

namespace App\Models\PolicyManagement\Campaign;

use App\Models\PolicyManagement\Campaign;
use Illuminate\Database\Eloquent\Model;

class CampaignAcknowledgment extends Model
{
    protected $table = 'policy_campaign_acknowledgments';
    protected $fillable = ['campaign_id', 'policy_id', 'user_id', 'token'];
    // protected $hidden = ['token'];

    /**
     * The policy that belong to the policy.
     */
    public function policy()
    {
        return $this->belongsTo(CampaignPolicy::class, 'policy_id');
    }

    /**
     * The policy that belong to the Policy user.
     */
    public function user()
    {
        return $this->belongsTo(CampaignGroupUser::class, 'user_id');
    }

    /**
     * The policy that belong to the Policy user.
     */
    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }
}
