<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class _Model extends Model
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
}
