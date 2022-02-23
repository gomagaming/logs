<?php

namespace GomaGaming\Logs\Services;

use GomaGaming\Logs\Models\Log;
use GomaGaming\Logs\Models\LogException;
use GomaGaming\Logs\Mail\LogReport;
use Illuminate\Support\Facades\Mail;


class LogService {
    
    protected $logs;
    protected $hashes;

    public function __construct(Log $logs, LogException $exceptions)
    {
        $this->logs = $logs;
        $this->exceptions = $exceptions;
    }

    protected function hashMessage($data)
    {
        return md5(
            $data['service'] . $data['env'] . $data['exception']['exception'] .
            $data['message'] . $data['exception']['file'] . $data['exception']['line']
        );
    }

    public function process($data)
    {
        $log = $this->logs->create($data)
                          ->createMetaData($data, 'headers')
                          ->createMetaData($data, 'params');

        if ($log->isException()) {
            $this->processException($log, $data);
        }
    }

    protected function processException($log, $data)
    {
        $data['exception']['hash'] = $this->hashMessage($data);

        if ( $exception = $this->exceptions->findByHash($data['exception']['hash']) ) {
            $exception->incrementHits()->reopen();
        }else{
            $data['exception']['trace'] = json_encode($data['exception']['trace']);
            $exception = $this->exceptions->create($data['exception']);
        }
        
        $log->associateException($exception);

        if (app('env') != 'local' && config('gomagaminglogs.send_report_email') && !$exception->hasBeenSent()) {
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
        
        $log->log_exception->setSent();
    }      
}