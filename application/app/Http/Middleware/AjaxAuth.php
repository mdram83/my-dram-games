<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AjaxAuth
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->ajax() || !Auth::check()){
            return new Response(Response::$statusTexts[Response::HTTP_UNAUTHORIZED], Response::HTTP_UNAUTHORIZED, );
        }
        return $next($request);
    }
}
