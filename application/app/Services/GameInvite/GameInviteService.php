<?php

namespace App\Services\GameInvite;

use App\Extensions\Core\GameOption\GameOptionValueConverter;
use App\Http\Controllers\ControllerValidationException;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameOption\GameOptionConfigurationCollection;
use MyDramGames\Core\GamePlay\GamePlayRepository;
use MyDramGames\Core\GameRecord\GameRecordRepository;

interface GameInviteService
{
    /**
     * @throws ControllerValidationException
     */
    public function getConfiguredGameInviteInputs(
        array $options,
        array $validatedInputs,
        GameOptionValueConverter $converter,
        GameOptionConfigurationCollection $configurations
    ): array;

    public function getJoinResponseContent(
        GameInvite $gameInvite,
        GamePlayRepository $gamePlayRepository,
        GameRecordRepository $gameRecordRepository,
    ): array;
}
