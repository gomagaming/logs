# GomaGaming Logs for Laravel/Lumen

## Description

- GomaGaming Logs tracks Exceptions, Requests and Responses and Custom logs and sends it to a specific queue with meta data associated (paths, parameters, headers, etc), which can then be properly treated.

## Laravel Version Compatibility
- Laravel `>= 8.x.x` on PHP `>= 7.3`

## Lumen Version Compatibility
- Lumen `>= 8.x.x` on PHP `>= 7.3`

## Installation

```
    composer require gomagaming/logs
```

### Lumen Only

Add GomaGamingLogsServiceProvider to bootstrap/app.php:

```
    $app->register(GomaGaming\Logs\GomaGamingLogsServiceProvider::class);
```

## Configuration file

By default all logs are being sent to a queue called 'logs', but can be changed in config file gomagaminglogs.php

### Lumen only:

```
    cp vendor/gomagaming/logs/config/gomagaminglogs.php config/gomagaminglogs.php
```

## Usage

Enable capturing unhandled exception by making the following change to App/Exceptions/Handler.php:

Laravel:

```
    use GomaGaming\Logs\GomaGamingLogs;

    public function register()
    {
        $this->reportable(function (Throwable $e) {

            if ($this->shouldReport($e)) {
                GomaGamingLogs::error($e->getMessage());
            }

        });
    }
```


Lumen:

```
    use GomaGaming\Logs\GomaGamingLogs;

    public function report(Throwable $exception)
    {
        if ($this->shouldReport($exception)) {
            GomaGamingLogs::error($exception->getMessage());
        }

        parent::report($exception);
    }
```

To capture information about all incoming requests or all responses, add the following middleware as global or apply it to a group:

In Laravel add it to app/Http/Kernel.php:

```
    protected $middleware = [
        (...)
        \GomaGaming\Logs\Http\Middleware\LogRequests::class,
        \GomaGaming\Logs\Http\Middleware\LogResponses::class,
    ];
```

In Lumen add it to bootstarp/app.php:

```
    $app->middleware([
        \GomaGaming\Logs\Http\Middleware\LogRequests::class,
        \GomaGaming\Logs\Http\Middleware\LogResponses::class
    ]);
```

Custom errors or informations can also be reported:

```
    use GomaGaming\Logs\GomaGamingLogs;

    GomaGamingLogs::info("This is an information about my service.");
    GomaGamingLogs::error("This is an error ocurring in my service");
```

For API's that don't want to give users information about exceptions, we can change render method in App/Exceptions/Handler.php to return always http code 500 with a custom message, example:


```
    public function render($request, Throwable $exception)
    {
        (...)

        //might want to check if request is asking for json response

        return response()->json(['status' => 'error', 'msg' => 'Internal Server Error'], 500);
    }
```


## TODO

Tests