<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Ajax
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->ajax()){
            return new Response(Response::$statusTexts[Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}
