<?php

return [

    'queue' => env('GOMAGAMINGLOGS_QUEUE', 'logs'),

    'process_jobs' => env('GOMAGAMINGLOGS_PROCESS_JOBS', false),

    'service_name' => env('GOMAGAMINGLOGS_SERVICE_NAME', config('app.name')),

    'env' => env('GOMAGAMINGLOGS_ENV', config('app.env')),

    'url' => env('GOMAGAMINGLOGS_URL', config('app.url')),

    'auth' => env('GOMAGAMINGLOGS_AUTH', false),

    'send_report_email' => env('GOMAGAMINGLOGS_SEND_REPORT_EMAIL', true),

    'emails' => explode(',', env('GOMAGAMINGLOGS_EMAILS') ?? ''),

    'request_msg' => env('GOMAGAMINGLOGS_REQUEST_MSG', 'Request'),

    'response_msg' => env('GOMAGAMINGLOGS_RESPONSE_MSG', 'Response: '),

    'jira' => [
        'create_issues' => env('GOMAGAMINGLOGS_JIRA_CREATE_ISSUES', false),
        'user_email' => env('GOMAGAMINGLOGS_JIRA_USER_EMAIL', ''),
        'user_api_token' => env('GOMAGAMINGLOGS_JIRA_USER_API_TOKEN', ''),
        'project_domain' => env('GOMAGAMINGLOGS_JIRA_PROJECT_DOMAIN', ''),
        'account_ids' => explode(', ', env('GOMAGAMINGLOGS_JIRA_ACCOUNT_IDS', '')),
        'issue_reporter' => env('GOMAGAMINGLOGS_JIRA_ISSUE_REPORTER', ''),
        'parent_issue' => env('GOMAGAMINGLOGS_JIRA_PARENT_ISSUE', ''),
    ],

];
