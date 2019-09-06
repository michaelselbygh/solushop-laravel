<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DevReport extends Mailable
{
    use Queueable, SerializesModels;

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
        return $this->from('alerts@solushop.com.gh', 'Solushop Ghana')
                    ->subject($this->data['subject'])
                    ->view('mail.alert')->with('data', $this->data)
                    ->attachFromStorage('/reports/sms/sms-report-'.date('Y-m-d').'.csv')
                    ->attachFromStorage('/reports/activity/activity-report-'.date('Y-m-d').'.csv');
    }
}
