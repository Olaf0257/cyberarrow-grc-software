<?php

namespace App\Mail\Compliance;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectNameUpdateNotification extends Mailable
{
    use Queueable;
    use SerializesModels;
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Project name update notification')->markdown('emails.compliance.project_name_update_notification', ['data' => $this->data]);
    }
}
