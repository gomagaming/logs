<?php

namespace GomaGaming\Logs\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'service', 'env', 'user_id', 'path', 'message', 'exception_id', 'trace', 'trace_counter'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function log_exception()
    {
        return $this->belongsTo(LogException::class);
    }

    public function metadata()
    {
        return $this->hasMany(LogMetaData::class);
    }

    public function scopeTrace($query, $trace)
    {
        return $query->where('trace', $trace);
    }

    // protected function serializeDate(\DateTimeInterface $date)
    // {
    //     return $date->format('Y-m-d');
    // }  

    public function isType($type)
    {
        return $this->type == $type;
    }  
    
    public function isException()
    {
        return $this->type == 'exception';
    }

    public function createMetaData($data, $type)
    {            
        $metadata = json_decode($data[$type], true);

        foreach ($metadata as $key => $value) {
            $this->metadata()->create([
                'type'  => $type,
                'key'   => $key,
                'value' => is_array($value) ? json_encode($value) : $value
            ]);
        }

        return $this;
    }    

    public function associateException($exception)
    {
        $this->log_exception()->associate($exception);
        $this->save();

        return $this;
    }

    public function getHeaders()
    {
        return $this->metadata()->type('headers')->get();
    }

    public function getParams()
    {
        return $this->metadata()->type('params')->get();
    }    

    public static function getLogServices()
    {
        return self::select('service')->groupBy('service')->get()->pluck('service')->toArray();
    }

    public static function getFilteredLogs($filters = [])
    {
        $query = self::orderBy('created_at', 'DESC');

        $page = $filters['page'] ?? 1;
        unset($filters['page']);

        $query = self::applyFilters($query, $filters, 'where');

        $query = $query->skip($page == 1 ? 0 : ($page - 1) * 10);

        return $query->paginate(10);
    }

    public static function getLogInfo($filters, $logId)
    {
        $log = self::with('log_exception', 'metadata')->find($logId);

        $query = self::with('log_exception', 'metadata')->orderBy('created_at', 'DESC')->trace($log->trace);

        $page = $filters['page'] ?? 1;

        $query = $query->skip($page == 1 ? 0 : ($page - 1) * 10);

        return ['log' => $log, 'logs-with-associated-trace' => $query->paginate(10)];
    }

    private static function applyFilters($query = null, $filters = [], $queryClause = 'where')
    {
        foreach($filters as $filterKey => $filterValue)
        {
            $query = $query->$queryClause($filterKey, $filterValue);
        }

        return $query;
    }
}   
