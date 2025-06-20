<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorcycleLog extends Model
{
    use HasFactory;
      protected $fillable = [
        'motorcycle_id',
        'event',
        'field',
        'old_value',
        'new_value',
        'changed_by',
    ];
}
