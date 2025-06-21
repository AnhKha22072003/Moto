<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ModelMotor;

class Maker extends Model
{
    use HasFactory;
    protected $table = 'maker';
    protected $primaryKey = 'code';
    protected $fillable = ['name'];
    public $timestamps = true;
  
    public function motorcycles()
    {
        return $this->hasMany(MotorCycle::class, 'maker_code', 'code');
    }
    public function models()
    {
        return $this->hasMany(ModelMotor::class, 'maker_code', 'code');
    }
}
