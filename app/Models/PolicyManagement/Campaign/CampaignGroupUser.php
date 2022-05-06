<?php

namespace App\Models\PolicyManagement\Campaign;

use Illuminate\Database\Eloquent\Model;

class CampaignGroupUser extends Model
{
    protected $table = 'policy_campaign_group_users';
    protected $appends = ['user_acknowledgement_status'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['group_id', 'email', 'first_name', 'last_name'];

    /**
     * gets campaing activities.
     */
    public function activities()
    {
        return $this->hasMany('App\Models\PolicyManagement\Campaign\CampaignActivity', 'user_id');
    }

    /**
     * gets campaing user acknowledgement tokens.
     */
    public function campaignAcknowledgmentUserTokens()
    {
        return $this->hasMany('App\Models\PolicyManagement\Campaign\CampaignAcknowledgmentUserToken', 'user_id');
    }

    /***
     * get user group
     */
    public function group()
    {
        return $this->belongsTo('App\Models\PolicyManagement\Campaign\CampaignGroup', 'group_id', 'id');
    }

    public function getUserAcknowledgementStatusAttribute()
    {
        $userPolicyCampaignAcknowledgementStatus = CampaignAcknowledgment::where('campaign_id', $this->group->campaign_id)->where('user_id', $this->id)->get();
        $userAcknowlwegeStatusCount = count($userPolicyCampaignAcknowledgementStatus);
        $completedAcknowledment = $userPolicyCampaignAcknowledgementStatus->wherein('status', 'completed')->count();
        $pendingAcknowledgement = $userPolicyCampaignAcknowledgementStatus->wherein('status', 'pending')->count();

        if ($userAcknowlwegeStatusCount == $completedAcknowledment) {
            $status = 'Acknowledged';
            $color = 'rgba(247, 184, 75, 0.85)';
        } elseif ($userAcknowlwegeStatusCount == $pendingAcknowledgement) {
            // check for queued email
            $exist_activity=CampaignActivity::where([['campaign_id', $this->group->campaign_id],['user_id',$this->id]])->first();
            if($exist_activity){
                if($exist_activity->type === "email-sent"){
                    $status = 'Email Sent';
                    $color = 'rgba(40, 167, 69, 0.85)';    
                }
                else{
                    $status = 'Email Sent Error';
                    $color = 'lightcoral';  
                }
                
            }
            else{
                $status = 'Email Queued';
                $color = 'rgb(91, 192, 222)';
            }
            
        } elseif ($completedAcknowledment != $pendingAcknowledgement || $completedAcknowledment == $pendingAcknowledgement) {
            $status = 'Partially Acknowledged';
            $color = 'red';
        }

        $statusColor = [
            'status' => $status,
            'color' => $color,
        ];

        return $statusColor;
    }
}
