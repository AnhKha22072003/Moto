<?php
namespace App\Filters\MotorCycle;

class ByMaker
{
    public function handle($query, \Closure $next)
    {
        if (request()->filled('maker_code')) {
            $query->where('maker_code', request()->input('maker_code'));
        }
 
        return $next($query);
    }
}