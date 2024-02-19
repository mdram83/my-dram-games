<?php

namespace App\Http\Middleware;

use App\Models\GameCore\Player\Player;
use App\Models\GameCore\Player\PlayerAnonymousFactory;
use App\Models\GameCore\Player\PlayerAnonymousRepository;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PlayerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cookieName = Config::get('player.playerHashCookieName');
        $cookieExpiration = Config::get('player.playerHashExpiration');

        if (Auth::check()) {

            $player = $request->user();
            Cookie::queue(Cookie::forget($cookieName));

        } else {

            try {
                $hash = Cookie::get($cookieName);

                if (isset($hash)) {
                    $player = App::make(PlayerAnonymousRepository::class)->getOne($hash);
                    $player->touch();

                } else {
                    // TODO first try to load existing player in case user removed cookie manually
                    $player = App::make(PlayerAnonymousFactory::class)->create(['key' => session()->getId()]);
                }

            } catch (Exception $e) {
                throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
            }

            Cookie::queue($cookieName, $player->hash, $cookieExpiration, null, null, false, false);
        }

        App::instance(Player::class, $player);
        View::share('player', $player);

        return $next($request);
    }
}
