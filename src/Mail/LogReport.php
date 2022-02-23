<?php

namespace GomaGaming\Logs\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use GomaGaming\Logs\Models\Log;

class LogReport extends Mailable
{
    use Queueable, SerializesModels;

    public $log;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject =  '[' . $this->log->env . '] ' . $this->log->service . ': ' . substr($this->log->message, 0, 100);
        
        return $this->markdown('gomagaming::emails.log_report')
                    ->subject($subject)
                    ->with([
                        'appName' => config('gomagaminglogs.service_name'),
                        'url' => config('gomagaminglogs.url') . "/".$this->log->id,
                        'headers' => $this->log->getHeaders(),
                        'params' => $this->log->getParams()
                    ]);
    }
}
