<?php

namespace GomaGaming\Logs;

use Illuminate\Support\Arr;
use Throwable;
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

    public static function exception(Throwable $exception)
    {
        $exceptionData = self::convertExceptionToArray($exception);

        return self::dispatch($exceptionData['exception']['message'], 'exception', $exceptionData);
    }        

    protected static function dispatch($message, $type, $data = [])
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

        if ($data) {
            $logData = array_merge($logData, $data);
        }

        dispatch((new LogJob($logData))->onQueue(config('gomagaminglogs.queue')));
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