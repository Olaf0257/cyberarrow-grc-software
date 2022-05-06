<?php

namespace App\Mail\ThirdPartyRisk;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $vendor;
    public $project;
    public $email_token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email_token, $project, $vendor)
    {
        $this->vendor = $vendor;
        $this->project = $project;
        $this->email_token = $email_token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.third-party-risk.reminder')->subject('Third Party Risk Questionnaire Reminder - '.$this->project->name);
    }
}
