<?php

namespace GomaGaming\Logs\Http\Middleware;

use Closure;
use GomaGaming\Logs\GomaGamingLogs;

class LogRequests
{
    public function handle($request, Closure $next)
    {
        GomaGamingLogs::request($request, config('gomagaminglogs.request_msg'));

        return $next($request);
    }
}