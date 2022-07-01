<?php

namespace GomaGaming\Logs\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogMetaData extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function log()
    {
        return $this->belongsTo(Log::class);
    }

    public function scopeType($q, $type)
    {
        return $q->where('type', $type);
    }    
}
