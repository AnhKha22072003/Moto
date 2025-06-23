<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorcycleLog extends Model
{
    use HasFactory;
    protected $table = 'motorcycle_logs';
    protected $fillable = [
        'motorcycle_id',
        'event',
        'field',
        'old_value',
        'new_value',
        'changed_by',
    ];
    public function motorcycle()
    {
        return $this->belongsTo(Motorcycle::class, 'motorcycle_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by', 'id');
    }
    public function scopeFilterFormDate(Builder $query)
    {
        if (request()->filled('from_date')) {
            $query->whereDate('created_at', '>=', request('from_date'));
        }

        return $query;
    }
    public function scopeFilterToDate(Builder $query)
    {
        if (request()->filled('to_date')) {
            $query->whereDate('created_at', '<=', request('to_date'));
        }
        return $query;
    }
    public function scopeFilterStatus(Builder $query)
    {
         if (request()->filled('status')) {
            $query->where('event', request('status'));
        }
           return $query;
    }
    public function scopeFilterMotorcycleId(Builder $query)
    {
          if (request()->filled('motorcycle_id')) {
            $query->where('motorcycle_id', request('motorcycle_id'));
        }
       return $query;
    }
  
    public function scopeSortByField(Builder $query, $sortBy, $sortOrder = 'asc')
    {
        return $query->when($sortBy, function ($q) use ($sortBy, $sortOrder) {
            $validOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'asc';
            return $q->orderBy($sortBy, $validOrder);
        });
    }
}
