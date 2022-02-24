<?php

namespace GomaGaming\Logs\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'service', 'env', 'user_id', 'path', 'message', 'exception_id', 'trace', 'trace_counter'];

    public function log_exception()
    {
        return $this->belongsTo(LogException::class);
    }

    public function metadata()
    {
        return $this->hasMany(LogMetaData::class);
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d');
    }  

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
                'value' => substr(is_array($value) ? reset($value) : $value, 0, 99)
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
}   