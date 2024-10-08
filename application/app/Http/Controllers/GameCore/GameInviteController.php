<?php

namespace App\Http\Controllers\GameCore;

use App\Extensions\Core\GameOption\GameOptionValueConverter;
use App\Services\PremiumPass\PremiumPass;
use App\Services\PremiumPass\PremiumPassException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ControllerException;
use Exception;
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
use MyDramGames\Core\Exceptions\GameSetupException;
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
use Symfony\Component\HttpKernel\Exception\HttpException;

class GameInviteController extends Controller
{
    public const string MESSAGE_PLAYER_JOINED = 'You have joined the game!';
    public const string MESSAGE_PLAYER_BACK = 'Welcome back!';
    public const string MESSAGE_REDIRECT_TITLE = 'Join the game!';
    public const string MESSAGE_INCORRECT_INPUTS = 'Incorrect inputs';

    public function __construct(readonly private PremiumPass $premiumPass)
    {

    }

    public function store(
        Request $request,
        GameInviteFactory $factory,
        Player $player,
        GameOptionValueConverter $converter,
        GameOptionConfigurationCollection $configurations,
    ): Response
    {
        try {
            $inputs = $this->getValidatedCastedStoreInputs($request, $converter, $configurations);

            $this->premiumPass->validate($inputs['slug'], $player);

            DB::beginTransaction();
            $gameInvite = $factory->create($inputs['slug'], $inputs['options'], $player);
            DB::commit();

            $responseContent = ['gameInvite' => $gameInvite->toArray()];
            return new Response($responseContent, SymfonyResponse::HTTP_OK);

        } catch (ControllerException $e) {
            return new Response(['message' => $e->getMessage()], SymfonyResponse::HTTP_BAD_REQUEST);

        } catch (PremiumPassException $e) {
            return response()->view('errors.403', ['exception' => $e], 403);

        }  catch (GameSetupException|GameBoxException|GameInviteException $e) {
            return new Response(['message' => $e->getMessage()], SymfonyResponse::HTTP_BAD_REQUEST);

        } catch (Exception) {
            DB::rollBack();
            return new Response(['message' => static::MESSAGE_INTERNAL_ERROR], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function joinRedirect(string $slug, int|string $gameInviteId): View
    {
        \Illuminate\Support\Facades\View::share('htmlHeadTitle', static::MESSAGE_REDIRECT_TITLE);
        \Illuminate\Support\Facades\View::share('slug', $slug);
        \Illuminate\Support\Facades\View::share('gameInviteId', $gameInviteId);

        return view('join-redirect');
    }

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

        } catch (PremiumPassException $e) {
            return response()->view('errors.403', ['exception' => $e], 403);

        } catch (GameInviteException $e) {
            return Redirect::route('games.show', ['slug' => $slug])->withErrors(['general' => $e->getMessage()]);

        } catch (Exception) {
            throw new HttpException(SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, static::MESSAGE_INTERNAL_ERROR);
        }
    }

    /**
     * @throws ControllerException|ValidationException
     */
    private function getValidatedCastedStoreInputs(
        Request $request,
        GameOptionValueConverter $converter,
        GameOptionConfigurationCollection $configurations,
    ): array
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|max:255',
            'options.numberOfPlayers' => 'required|integer|min:1',
            'options.autostart' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            $message = json_encode(['message' => static::MESSAGE_INCORRECT_INPUTS, 'errors' => $validator->errors()]);
            throw new ControllerException($message);
        }

        $validated = $validator->validated();
        $inputs = [
            'slug' => $validated['slug'],
            'options' => array_merge($request->get('options'), $validated['options']),
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
            throw new ControllerException(json_encode(['message' => $e->getMessage()]));
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
