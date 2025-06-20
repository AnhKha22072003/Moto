<?php
namespace App\Filters\MotorCycle;

use Closure;

class ByYear
{
      public function handle($query, Closure $next)
    {
        if (request()->filled('from_year')) {
            $query->whereDate('motorcycle_nensiki ', '>=', request()->input('from_year'));
        }
 
        if (request()->filled('to_year')) {
            $query->whereDate('motorcycle_nensiki ', '<=', request()->input('to_year'));
        }
 
        return $next($query);
    }
}