<?php

namespace GomaGaming\Logs\Services;

use GomaGaming\Logs\Models\Log;
use GomaGaming\Logs\Models\LogHash;
use GomaGaming\Logs\Mail\LogReport;
use Illuminate\Support\Facades\Mail;


class LogService {
    
    protected $logs;
    protected $hashes;

    public function __construct(Log $logs, LogHash $hashes)
    {
        $this->logs = $logs;
        $this->hashes = $hashes;
    }

    protected function hashMessage($message)
    {
        return md5($message);
    }

    public function process($data)
    {
        $log = $this->logs->create($data)
                          ->createMetaData($data, 'headers')
                          ->createMetaData($data, 'params');

        if ($log->isType('error')) {
            $this->processTypeError($log);
        }
    }

    protected function processTypeError($log)
    {
        $hashedMessage = $this->hashMessage($log->message);

        if ( $hash = $this->hashes->findByHash($hashedMessage) ) {
            $hash->incrementHits()->reopen();
        }else{
            $hash = $this->hashes->create(['hash' => $hashedMessage]);
        }
        
        $log->associateHash($hash);

        if (/*app('env') != 'local' &&*/ config('gomagaminglogs.send_report_email') && !$hash->hasBeenSent()) {
            $this->report($log);
        }        
    }

    protected function report($log)
    {
        if ( !$emails = config('gomagaminglogs.emails') ) {
            return;
        }

        foreach ($emails as $email) {
            Mail::to($email)->send(new LogReport($log));
        }
        
        $log->logHash->setSent();
    }      
}