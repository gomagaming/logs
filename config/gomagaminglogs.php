<?php

return [

    'queue' => env('GOMAGAMINGLOGS_QUEUE', 'logs'),

    'process_jobs' => env('GOMAGAMINGLOGS_PROCESS_JOBS', false),

    'service_name' => env('GOMAGAMINGLOGS_SERVICE_NAME', config('app.name')),

    'env' => env('GOMAGAMINGLOGS_ENV', config('app.env')),

    'url' => env('GOMAGAMINGLOGS_URL', config('app.url')),

    'send_report_email' => env('GOMAGAMINGLOGS_SEND_REPORT_EMAIL', true),

    'emails' => explode(',', env('GOMAGAMINGLOGS_EMAILS')),

    'request_msg' => env('GOMAGAMINGLOGS_REQUEST_MSG', 'Request'),

    'response_msg' => env('GOMAGAMINGLOGS_RESPONSE_MSG', 'Response: '),

];