<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Maker;

class ModelMotor extends Model
{
    use HasFactory;

    protected $table = 'model';
    protected $primaryKey = 'code';
    protected $fillable = [
        'name',
    ];
    public function motorcycles()
    {
        return $this->hasMany(MotorCycle::class, 'model_code', 'code');
    }
    public function maker()
    {
        return $this->belongsTo(Maker::class, 'maker_code', 'code');
    }
}
