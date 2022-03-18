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
            $data['exception']['file'] . $data['exception']['line']
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
            $exception = $this->exceptions->create($this->prepareExceptionData($data));
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
            Mail::to(trim($email))->send(new LogReport($log));
        }
        
        $log->log_exception->setSent();
    }      

    protected function prepareExceptionData($data)
    {
        $data['exception']['trace'] = json_encode($data['exception']['trace']);
        $data['exception']['service'] = $data['service'];
        $data['exception']['env'] = $data['env'];

        return $data['exception'];
    }
}