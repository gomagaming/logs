<?php

namespace GomaGaming\Logs\Http\Middleware;

use Closure;
use GomaGaming\Logs\GomaGamingLogs;

class LogResponses
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        GomaGamingLogs::info(config('gomagaminglogs.response_msg') . $response->status());

        return $response;
    }
}