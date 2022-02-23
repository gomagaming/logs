<?php

namespace GomaGaming\Logs\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogException extends Model
{
    use HasFactory;

    protected $fillable = ['hash', 'hits', 'sent', 'status', 'message', 'exception', 'file', 'line', 'trace'];

    public function logs()
    {
        return $this->hasMany(Log::class);
    }

    public function scopeHash($query, $hash)
    {
        return $query->where('hash', $hash);
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function hasBeenSent()
    {
        return $this->sent;
    }

    public function isStatus($status)
    {
        return $this->status == $status;
    }

    public function reopen()
    {
        $this->status = 'pending';
        $this->save();

        return $this;
    }

    public function setSent()
    {
        $this->sent = 1;
        $this->save();

        return $this;
    }        

    public function incrementHits()
    {
        $this->hits++;
        $this->save();

        return $this;
    }

    public function findByHash($hash)
    {
        return self::hash($hash)->first();
    }
}