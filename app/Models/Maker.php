<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maker extends Model
{
    use HasFactory;
    protected $table = 'maker';
    protected $primaryKey = 'code';
    protected $fillable = ['name'];
    public $timestamps = true;
    // protected $casts = [
    //     'code' => 'integer',
    // ];
    /**
     * Get the motorcycles associated with the maker.
     */
    public function motorcycles()
    {
        return $this->hasMany(MotorCycle::class, 'maker_code', 'code');
    }
}
