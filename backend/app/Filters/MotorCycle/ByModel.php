<?php
namespace App\Filters\MotorCycle;

class ByModel
{
    public function handle($query, \Closure $next)
    {
        if (request()->filled('model_code')) {
            $query->where('model_code', request()->input('model_code'));
        }

        return $next($query);
    }
}