<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
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
            return new Response(Controller::MESSAGE_UNAUTHORIZED, Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}
