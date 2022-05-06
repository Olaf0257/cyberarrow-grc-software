<?php

namespace App\Models\PolicyManagement\Campaign;

use Mews\Purifier\Casts\CleanHtml;
use App\Models\DataScope\BaseModel;
class Campaign extends BaseModel
{
    protected $table = 'policy_campaigns';
    protected $fillable = ['name', 'owner_id', 'launch_date', 'auto_enroll_users', 'due_date', 'timezone', 'acknowledgement_email_sent'];

    protected $casts = [
        'name'    => CleanHtml::class,
        'launch_date'    => CleanHtml::class,
        'auto_enroll_users'    => CleanHtml::class,
        'due_date'    => CleanHtml::class,
        'timezone'    => CleanHtml::class,
        'acknowledgement_email_sent'    => CleanHtml::class,
    ];

    /**
     * The policies that belong to the campaign.
     */
    public function policies()
    {
        return $this->hasMany('App\Models\PolicyManagement\Campaign\CampaignPolicy', 'campaign_id');
    }

    /**
     * The groups that belong to the campaign.
     */
    public function groups()
    {
        return $this->hasMany('App\Models\PolicyManagement\Campaign\CampaignGroup', 'campaign_id');
    }

    /**
     * The users that belong to the campaign.
     */
    public function users()
    {
        return $this->hasManyThrough(
            CampaignGroupUser::class,
            CampaignGroup::class,
            'campaign_id', // Foreign key on the intermediate table...
            'group_id' // Foreign key on the final table...
        );
    }

    /**
     * The acknowledgements that belong to the campaign.
     */
    public function acknowledgements()
    {
        return $this->hasMany('App\Models\PolicyManagement\Campaign\CampaignAcknowledgment', 'campaign_id');
    }

    /**
     * gets campaing activities.
     */
    public function activities()
    {
        return $this->hasMany('App\Models\PolicyManagement\Campaign\CampaignActivity', 'campaign_id');
    }

    /**
     * Get campaign owner.
     */
    public function owner()
    {
        return $this->belongsTo('App\Models\UserManagement\Admin', 'owner_id');
    }
}
