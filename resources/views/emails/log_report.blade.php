@component('mail::message')
# New Alert from {{ $log->service }}

## Environment: {{ $log->env }}

@component('mail::panel')
    {{ $log->log_exception->exception }} - {{ $log->log_exception->message }}

    {{ $log->log_exception->file }} - {{ $log->log_exception->line }}
@endcomponent

@if ($headers->count())
@component('mail::table')
| Header       | Value          |
| ------------- |:-------------:|
@foreach ($headers as $key => $header)
| {{ $header->key }}      | {{ $header->value }} |
@endforeach
@endcomponent
@endif

@if ($params->count())
    @component('mail::table')
        | Header       | Value          |
        | ------------- |:-------------:|
        @foreach ($params as $key => $param)
        | {{ $param->key }}      | {{ $param->value }} |
        @endforeach
    @endcomponent
@endif

@component('mail::button', ['url' => $url ])
Check Full Details
@endcomponent


Thanks,<br>
{{ $appName }}
@endcomponent