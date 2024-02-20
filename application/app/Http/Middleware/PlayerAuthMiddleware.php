<?php

namespace App\Http\Middleware;

use App\GameCore\Player\PlayerAnonymousRepository;
use Closure;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class PlayerAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            $hash = $request->query(Config::get('player.playerHashCookieName'));
            $hash = App::make(Encrypter::class)->decrypt($hash, false);
            $hash = CookieValuePrefix::remove($hash);

            $player = App::make(PlayerAnonymousRepository::class)->getOne($hash);
            Auth::login($player);
        }

        return $next($request);
    }
}
