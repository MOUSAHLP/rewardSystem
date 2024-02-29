<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class langMiddleware
{

    public function handle(Request $request, Closure $next): Response
    {
        $locale="en";
        if( in_array($request->header("lang"), ["ar","en"])){
            $locale = $request->header("lang");
        }
        app()->setLocale($locale);
        return $next($request);
    }
}
