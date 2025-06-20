<?php
namespace App\Filters\MotorCycle;

use Closure;

class ByOdo
{
    public function handle($query, Closure $next)
    {
        if (request()->filled('from_odo')) {
            $query->whereDate('soukou', '>=', request()->input('from_odo'));
        }
 
        if (request()->filled('to_odo')) {
            $query->whereDate('soukou', '<=', request()->input('to_odo'));
        }
 
        return $next($query);
    }
}