<?php

namespace App\Mail\PolicyManagement;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignCompleted extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $campaign;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.policy-management.campaign-completed')->subject('Completion of policy campaign - '.$this->campaign->name);
    }
}
