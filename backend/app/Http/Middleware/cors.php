<?php

namespace App\Http\Middleware;

use Closure;

class cors
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

        $origins = env('CORS_ORIGINS', []);

        return $next($request)
            ->header('Access-Control-Allow-Origin', explode(",", $origins))
            ->header('Access-Control-Allow-Headers','*')
            ->header('Access-Control-Allow-Methods','*');

    }
}
