<?php

namespace GomaGaming\Logs;

use GomaGaming\Logs\Jobs\LogJob;

class GomaGamingLogs 
{

    public static function error($message)
    {    
        return self::dispatch($message, 'error');
    }

    public static function info($message)
    {    
        return self::dispatch($message, 'info');
    }    

    private static function dispatch($message, $type)
    {
        $logData = [
            'service' => config('app.name'), 
            'type'    => $type,
            'message' => $message,
            'path'    => request()->getPathInfo(),
            'headers' => json_encode(request()->headers->all()),
            'params'  => json_encode(request()->all())
        ];

        dispatch((new LogJob($logData))->onQueue(config('gomagaminglogs.queue')));
    }
}