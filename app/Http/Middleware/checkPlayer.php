<?php

namespace App\Http\Middleware;

use Closure;

class checkPlayer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user() &&  Auth::user()->admin == 0) {
            return $next($request);
        }

        return response()->json(['msg'=>'Player option only.'], 401);
    }
}