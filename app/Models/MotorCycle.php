<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MotorCycle extends Model
{
    use HasFactory;
    protected $table = 'motorcycle';
    protected $cloneOriginId;
    protected $bulkUpdate = false;
    protected $fillable = [
        'searchkey',
        'maker_code',
        'model_code',
        'type',
        'ippan_kakaku',
        'nensiki',
        'soukou',
        'soukou_fumei_flg',
        'haikiryo',
        'color_code',
        'color',
        'noridasi_kakaku',
        'iyoukyo',
        'photo1',
        'photo1_pr',
        'photo2',
        'photo2_pr',
        'photo3',
        'photo3_pr',
        'photo4',
        'photo4_pr',
        'photo5',
        'photo5_pr',
        'photo6',
        'photo6_pr',
        'photo7',
        'photo7_pr',
        'photo8',
        'photo8_pr',
        'photo9',
        'photo9_pr',
        'photo10',
        'photo10_pr',
        'grade',
        'shatai_bangou',
        'first_year_registration',
        'last_update_id'
    ];

    public $timestamps = true;
    public function getCloneOriginId()
    {
        return $this->cloneOriginId;
    }
    public function setCloneOriginId($id)
    {
        $this->cloneOriginId = $id;
    }

    public function setBulkUpdate()
    {
        $this->bulkUpdate = true;
    }

    public function getBulkUpdate()
    {
        return $this->bulkUpdate;
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'last_update_id', 'id');
    }
    public function maker()
    {
        return $this->belongsTo(Maker::class, 'maker_code', 'code');
    }
    public function model()
    {
        return $this->belongsTo(ModelMotor::class, 'model_code', 'code');
    }
    public function logs()
    {
        return $this->hasMany(MotorcycleLog::class, 'motorcycle_id', 'id');
    }
    public function scopeFilterMaker(Builder $query, $maker)
    {
        return $query->when($maker, fn($q) => $q->where('maker_code', $maker));
    }

    public function scopeFilterModel(Builder $query, $model)
    {
        return $query->when($model, fn($q) => $q->where('model_code', $model));
    }

    public function scopeFilterNensiki(Builder $query, $nensiki)
    {
        return $query->when($nensiki, fn($q) => $q->where('nensiki', $nensiki));
    }

    public function scopeFilterMaxOdo(Builder $query, $maxOdo)
    {
        return $query->when($maxOdo, fn($q) => $q->where('soukou', '<=', $maxOdo));
    }

    public function scopeFilterMaxPrice(Builder $query, $maxPrice)
    {
        return $query->when($maxPrice, fn($q) => $q->where('ippan_kakaku', '<=', $maxPrice));
    }

    public function scopeSortByField(Builder $query, $sortBy, $sortOrder = 'asc')
    {
        return $query->when($sortBy, function ($q) use ($sortBy, $sortOrder) {
            $validOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'asc';
            return $q->orderBy($sortBy, $validOrder);
        });
    }
}
