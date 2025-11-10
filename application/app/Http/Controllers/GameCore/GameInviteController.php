<?php

namespace App\Http\Controllers\GameCore;

use App\Extensions\Core\GameOption\GameOptionValueConverter;
use App\Http\Requests\GameCore\GameInviteStoreRequest;
use App\Services\GameInvite\GameInviteService;
use App\Services\PremiumPass\PremiumPass;
use App\Services\PremiumPass\PremiumPassException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerValidationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use MyDramGames\Core\Exceptions\GameBoxException;
use MyDramGames\Core\Exceptions\GameInviteException;
use MyDramGames\Core\GameInvite\GameInviteFactory;
use MyDramGames\Core\GameInvite\GameInviteRepository;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollection;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Core\GameRecord\GameRecordRepository;
use MyDramGames\Utils\Player\Player;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class GameInviteController extends Controller
{
    public const string MESSAGE_PLAYER_JOINED = 'You have joined the game!';
    public const string MESSAGE_PLAYER_BACK = 'Welcome back!';
    public const string MESSAGE_REDIRECT_TITLE = 'Join the game!';

    public function __construct(readonly private PremiumPass $premiumPass)
    {

    }

    /**
     * @throws ControllerValidationException
     * @throws PremiumPassException
     * @throws ValidationException
     */
    public function store(
        GameInviteStoreRequest $request,
        GameInviteFactory $factory,
        Player $player,
        GameOptionValueConverter $converter,
        GameOptionConfigurationCollection $configurations,
        GameInviteService $gameInviteService,
    ): Response
    {
        $inputs = $gameInviteService->getConfiguredGameInviteInputs(
            $request->get('options'),
            $request->validated(),
            $converter,
            $configurations
        );
        $this->premiumPass->validate($inputs['slug'], $player);
        $gameInvite = DB::transaction(fn() => $factory->create($inputs['slug'], $inputs['options'], $player));

        return new Response(['gameInvite' => $gameInvite->toArray()], SymfonyResponse::HTTP_OK);
    }

    public function joinRedirect(string $slug, int|string $gameInviteId): View
    {
        \Illuminate\Support\Facades\View::share('htmlHeadTitle', static::MESSAGE_REDIRECT_TITLE);
        \Illuminate\Support\Facades\View::share('slug', $slug);
        \Illuminate\Support\Facades\View::share('gameInviteId', $gameInviteId);

        return view('join-redirect');
    }

    /**
     * @throws GameBoxException
     * @throws PremiumPassException
     */
    public function join(
        GameInviteRepository $repository,
        GamePlayRepository $gamePlayRepository,
        GameRecordRepository $gameRecordRepository,
        Player $player,
        GameInviteService $gameInviteService,
        string $slug,
        int|string $gameInviteId
    ): View|Response|RedirectResponse
    {
        try {

            $gameInvite = $repository->getOne($gameInviteId);

            if (!$gameInvite->isPlayer($player)) {
                $this->premiumPass->validate($gameInvite->getGameBox()->getSlug(), $player);
                $gameInvite->addPlayer($player);
                $message = static::MESSAGE_PLAYER_JOINED;
            }

            Session::flash('success', ($message ?? static::MESSAGE_PLAYER_BACK));

            return view(
                'single',
                $gameInviteService->getJoinResponseContent($gameInvite, $gamePlayRepository, $gameRecordRepository)
            );

        } catch (GameInviteException $e) {
            return Redirect::route('games.show', ['slug' => $slug])->withErrors(['general' => $e->getMessage()]);
        }
    }
}
