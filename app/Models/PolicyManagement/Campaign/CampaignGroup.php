<?php

namespace App\Models\PolicyManagement\Campaign;

use Illuminate\Database\Eloquent\Model;

class CampaignGroup extends Model
{
    protected $table = 'policy_campaign_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['campaign_id', 'group_id', 'name'];

    /**
     * The users that belong to the group.
     */
    public function users()
    {
        return $this->hasMany('App\Models\PolicyManagement\Campaign\CampaignGroupUser', 'group_id');
    }
}
