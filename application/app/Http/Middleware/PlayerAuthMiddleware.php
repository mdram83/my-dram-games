<?php

namespace App\Http\Middleware;

use App\Extensions\Utils\Player\PlayerAnonymousRepository;
use Closure;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

readonly class PlayerAuthMiddleware
{
    public function __construct(
        private PlayerAnonymousRepository $playerAnonymousRepository,
        private Encrypter $encrypter,
    )
    {

    }

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
            $hash = $this->encrypter->decrypt($hash, false);
            $hash = CookieValuePrefix::remove($hash);

            $player = $this->playerAnonymousRepository->getOne($hash);
            Auth::login($player);
        }

        return $next($request);
    }
}
