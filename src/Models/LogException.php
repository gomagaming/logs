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

    public static function getPaginatedLogsByException($logExceptionId)
    {
        $logException = self::find($logExceptionId);

        if (!$logException){
            return;
        }

        return $logException->logs()->with('metadata')->paginate(10);
    }

    public static function getFilteredLogExceptions($filters = [])
    {
        $query = self::query();

        $query = self::applyFilters($query, $filters['orders'] ?? [], 'orderBy');
    
        $query = self::applyFilters($query, $filters['filters'] ?? [], 'where');

        return $query->paginate(10);
    }

    private static function applyFilters($query = null, $filters = [], $queryClause)
    {
        foreach($filters as $filterKey => $filterValue)
        {
            $query = $query->$queryClause($filterKey, $filterValue);
        }

        return $query;
    }
}
