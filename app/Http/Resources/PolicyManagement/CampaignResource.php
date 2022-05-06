<?php

namespace App\Http\Resources\PolicyManagement;

use App\Models\GlobalSettings\GlobalSetting;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    private $appTimezone;
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->appTimezone = GlobalSetting::query()->first('timezone')->timezone;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $completedAcknowledgmentPercentage = 0;
        $acknowledgments = $this->acknowledgements;

        $totalAcknowledgment = $acknowledgments->count();
        $completedAcknowledgment = $acknowledgments->where('status', 'completed')->count();

        if($totalAcknowledgment && $completedAcknowledgment){
            $completedAcknowledgmentPercentage = ($completedAcknowledgment/$totalAcknowledgment) * 100;
        }

        $nowDateTime = Carbon::now($this->appTimezone);
        $campaignLaunchDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->launch_date, 'UTC')->setTimezone($this->appTimezone);
        $campaignDueDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->due_date, 'UTC')->setTimezone($this->appTimezone);



        if($nowDateTime->lessThan($campaignLaunchDate)){
            $campaignStatusBadge = 'bg-danger';
            $campaignStatusBadgeText = 'Not Started';
        } else {

            if($this->status == 'active'){
                $campaignStatusBadge = 'bg-info';
                $campaignStatusBadgeText = 'In progress';
            } else {
                $campaignStatusBadge = 'bg-success';
                $campaignStatusBadgeText = 'Completed';
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'policies' => $this->policies->count(),
            'status' => $campaignStatusBadgeText,
            'status_badge' => $campaignStatusBadge,
            'start_date' => $campaignLaunchDate,
            'due_date' => $campaignDueDate,
            'acknowledgments' => $completedAcknowledgmentPercentage
        ];
    }
}
