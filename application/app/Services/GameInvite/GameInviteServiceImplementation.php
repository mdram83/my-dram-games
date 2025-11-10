<?php

namespace App\Services\GameInvite;

use App\Extensions\Core\GameOption\GameOptionValueConverter;
use App\Http\Controllers\ControllerValidationException;
use Illuminate\Support\Facades\App;
use MyDramGames\Core\Exceptions\GameOptionValueException;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameOption\GameOptionConfiguration;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollection;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Core\GameRecord\GameRecordRepository;
use MyDramGames\Utils\Exceptions\CollectionException;

class GameInviteServiceImplementation implements GameInviteService
{
    public function getConfiguredGameInviteInputs(
        array $options,
        array $validatedInputs,
        GameOptionValueConverter $converter,
        GameOptionConfigurationCollection $configurations
    ): array {

        $inputs = [
            'slug' => $validatedInputs['slug'],
            'options' => array_merge($options, $validatedInputs['options']),
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

    public function getJoinResponseContent(
        GameInvite $gameInvite,
        GamePlayRepository $gamePlayRepository,
        GameRecordRepository $gameRecordRepository,
    ): array {

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
