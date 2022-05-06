<?php

namespace App\Mail\ThirdPartyRisk;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Questionnaire extends Mailable
{
    use Queueable, SerializesModels;

    public $vendor;
    public $project;
    public $vendor_token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($vendor_token, $project, $vendor)
    {
        $this->vendor = $vendor;
        $this->project = $project;
        $this->vendor_token = $vendor_token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.third-party-risk.questionnaire')->subject('Third Party Risk Questionnaire - '.$this->project->name);
    }
}
