<?php

namespace App\Http\Middleware;

use App\GameCore\Player\PlayerAnonymousFactory;
use App\GameCore\Player\PlayerAnonymousRepository;
use App\GameCore\Services\HashGenerator\HashGenerator;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;
use MyDramGames\Utils\Player\Player;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

readonly class PlayerMiddleware
{
    public function __construct(
        private PlayerAnonymousFactory $playerFactory,
        private PlayerAnonymousRepository $playerRepository,
    )
    {

    }

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

                if (isset($hash) && $player = $this->playerRepository->getOne($hash)) {

                    $player->touch();

                } else {

                    $key = session()->getId();
                    $player =
                        $this->playerRepository->getOne(App::make(HashGenerator::class)->generateHash($key))
                        ?? $this->playerFactory->create(['key' => $key]);
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
