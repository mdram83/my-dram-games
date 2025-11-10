<?php

namespace App\Http\Controllers\GameCore;

use App\Extensions\Core\GameOption\GameOptionValueConverter;
use App\Http\Requests\GameCore\GameInviteStoreRequest;
use App\Services\PremiumPass\PremiumPass;
use App\Services\PremiumPass\PremiumPassException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerValidationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MyDramGames\Core\Exceptions\GameBoxException;
use MyDramGames\Core\Exceptions\GameInviteException;
use MyDramGames\Core\Exceptions\GameOptionValueException;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteFactory;
use MyDramGames\Core\GameInvite\GameInviteRepository;
use MyDramGames\Core\GameOption\GameOptionConfiguration;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollection;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Core\GameRecord\GameRecordRepository;
use MyDramGames\Utils\Exceptions\CollectionException;
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
    ): Response
    {
        $inputs = $this->getConfiguredInputs($request->get('options'), $request->validated(), $converter, $configurations);
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
        string $slug,
        int|string $gameInviteId
    ): View|Response|RedirectResponse
    {
        try {

            $gameInvite = $repository->getOne($gameInviteId);

            $this->premiumPass->validate($gameInvite->getGameBox()->getSlug(), $player);

            if (!$gameInvite->isPlayer($player)) {
                $gameInvite->addPlayer($player);
                $message = static::MESSAGE_PLAYER_JOINED;
            }

            $responseContent = $this->getJoinResponseContent($gameInvite, $gamePlayRepository, $gameRecordRepository);

            Session::flash('success', ($message ?? static::MESSAGE_PLAYER_BACK));

            return view('single', $responseContent);

        } catch (GameInviteException $e) {
            return Redirect::route('games.show', ['slug' => $slug])->withErrors(['general' => $e->getMessage()]);

        }
    }

    /**
     * @throws ControllerValidationException
     */
    private function getConfiguredInputs(
        array $allOptions,
        array $validatedInputs,
        GameOptionValueConverter $converter,
        GameOptionConfigurationCollection $configurations,
    ): array
    {
        $inputs = [
            'slug' => $validatedInputs['slug'],
            'options' => array_merge($allOptions, $validatedInputs['options']),
        ];

        try {
            $configurations->reset();
            foreach ($inputs['options'] as $key => $value) {
                $configuration = App::makeWith(GameOptionConfiguration::class, [
                    'optionKey' => $key, 'optionValue' => $converter->convert($value, $key)
                ]);
                $configurations->add($configuration);
            }
        } catch (GameOptionValueException|CollectionException $e) {
            throw new ControllerValidationException(json_encode(['message' => $e->getMessage()]));
        }

        $inputs['options'] = $configurations;

        return $inputs;
    }

    private function getJoinResponseContent(
        GameInvite $gameInvite,
        GamePlayRepository $gamePlayRepository,
        GameRecordRepository $gameRecordRepository,
    ): array
    {
        $gamePlay = $gamePlayRepository->getOneByGameInvite($gameInvite);

        $responseContent = [
            'gameBox' => $gameInvite->getGameBox()->toArray(),
            'gameInvite' => $gameInvite->toArray(),
            'gamePlayId' => $gamePlay?->getId(),
        ];

        if ($gamePlay?->isFinished()) {
            $responseContent['gameRecords'] = array_map(fn($record) =>
                [
                    'player' => $record->getPlayer()->getName(),
                    'score' => $record->getScore(),
                    'isWinner' => $record->isWinner(),
                ],
                $gameRecordRepository->getByGameInvite($gameInvite)->toArray()
            );
        }

        return $responseContent;
    }
}
