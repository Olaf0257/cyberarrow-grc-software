<?php

namespace App\Exports\PolicyManagement\Campaigns;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class usersStatusExport implements FromCollection
{
    private $campaign;

    public function __construct($campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $campaignActivities = $this->campaign->activities()->with('user', 'campaign.policies')->get();

        $campaignActivitiesCollection = [
            [
                'first_name',
                'last_name',
                'email',
                'policies',
                'status',
                'acknowledge_date',
                'sent_date',
            ],
        ];

        foreach ($campaignActivities as $campaignActivity) {
            $policies = $campaignActivity->campaign->policies->pluck('display_name')->toArray();
            $acknowledgeDate = '';
            $sentDate = '';

            if ($campaignActivity->type == 'email-sent') {
                $sentDate = $campaignActivity->created_at->format('d-m-Y H:i:s');
            }
            if ($campaignActivity->type == 'policy-acknowledged') {
                $acknowledgeDate = $campaignActivity->created_at->format('d-m-Y H:i:s');
            }

            $campaignActivitiesCollection[] = [
                $campaignActivity->user->first_name,
                $campaignActivity->user->last_name,
                $campaignActivity->user->email,
                implode(',', $policies),
                $campaignActivity->type,
                $acknowledgeDate,
                $sentDate,
            ];
        }

        return new Collection(
            $campaignActivitiesCollection
        );
    }
}
