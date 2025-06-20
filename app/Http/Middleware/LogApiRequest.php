<?php

namespace App\Http\Middleware;

use App\Models\LogApi;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogApiRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
       if ($request->is('api/*')) {
        $data=[
            'user' =>Auth::user()->id,
            'ip' => $request->ip(),
            'method' => $request->method(),
             'url' => $request->fullUrl(),
            'params' => json_encode($request->all()),
        ];
        Log::info('API Request',$data);
        LogApi::create([
            'user_id' => Auth::check() ? Auth::id() : null,
            'ip_address' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'params' => json_encode($request->all()),
        ]);
    }
        return $next($request);
    }
}
