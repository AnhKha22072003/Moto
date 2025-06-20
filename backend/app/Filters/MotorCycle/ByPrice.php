<?php
namespace App\Filters\MotorCycle;

class ByPrice
{
    public function handle($query, \Closure $next)
    {
        if (request()->filled('from_price')) {
            $query->where('ippan_kakaku', '>=', request()->input('from_price'));
        }

        if (request()->filled('to_price')) {
            $query->where('ippan_kakaku', '<=', request()->input('to_price'));
        }

        return $next($query);
    }
} 