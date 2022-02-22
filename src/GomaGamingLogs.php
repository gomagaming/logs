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
            'service' => config('gomagaminglogs.service_name'),
            'env'     => config('gomagaminglogs.env'),
            'type'    => $type,
            'message' => $message,
            'user_id' => auth()->user() ? auth()->guard(config('gomagaminglogs.guard'))->user()->id : null,
            'path'    => request()->getPathInfo(),
            'headers' => json_encode(request()->headers->all()),
            'params'  => json_encode(request()->all()),
            'created_at' => date('Y-m-d H:i:s')
        ];

        dispatch((new LogJob($logData))->onQueue(config('gomagaminglogs.queue')));
    }
}