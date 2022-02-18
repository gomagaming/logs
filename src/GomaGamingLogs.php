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

    public static function beforeRequest($message = null)
    {
        $message = $message ? $message : 'Middleware before request';

        return self::info($message);
    }

    public static function beforeResponse($message = null)
    {
        $message = $message ? $message : 'Middleware before response';

        return self::info($message);
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