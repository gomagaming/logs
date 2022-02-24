<?php

namespace GomaGaming\Logs;

use Illuminate\Support\Arr;
use Throwable;
use GomaGaming\Logs\Jobs\LogJob;

class GomaGamingLogs 
{
    protected static $trace = null;

    protected static function generateTrace()
    {
        self::$trace = bin2hex(random_bytes(20));

        return self::$trace;
    }

    protected static function isTraceNull()
    {
        return self::$trace == null;
    }

    public static function getTrace()
    {
        return self::$trace;
    }    

    public static function info($message, $data = [])
    {    
        return self::dispatch($message, 'info', $data);
    }

    public static function error($message, $data = [])
    {    
        return self::dispatch($message, 'error', $data);
    }

    public static function exception(Throwable $exception, $data = [])
    {
        $data = array_merge($data, self::convertExceptionToArray($exception));

        return self::dispatch($data['exception']['message'], 'exception', $data);
    }        

    protected static function dispatch($message, $type, $data = [])
    {
        $logData = [
            'service'    => config('gomagaminglogs.service_name'),
            'env'        => config('gomagaminglogs.env'),
            'type'       => $type,
            'message'    => $message,
            'user_id'    => config('gomagaminglogs.auth') ? self::getUserId() : null,
            'path'       => request()->getPathInfo(),
            'headers'    => json_encode(request()->headers->all()),
            'params'     => json_encode(request()->all()),
            'trace'      => self::isTraceNull() ? self::generateTrace() : self::getTrace(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($data) {
            $logData = array_merge($logData, $data);
        }

        dispatch((new LogJob($logData))->onQueue(config('gomagaminglogs.queue')));
    }

    protected static function getUserId()
    {
        return auth()->user() ? auth()->user()->id : null;
    }

    protected static function convertExceptionToArray(Throwable $e)
    {
        return ['exception' => 
            [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->map(function ($trace) {
                    return Arr::except($trace, ['args']);
                })->all(),        
            ]
        ];
    }

}