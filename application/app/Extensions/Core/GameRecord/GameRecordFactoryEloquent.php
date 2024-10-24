<?php

namespace App\Extensions\Core\GameRecord;

use App\Models\GameInviteEloquentModel;
use App\Models\GameRecordEloquentCoreModel;
use Illuminate\Database\UniqueConstraintViolationException;
use MyDramGames\Core\Exceptions\GameRecordException;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameRecord\GameRecord;
use MyDramGames\Core\GameRecord\GameRecordFactory;
use MyDramGames\Utils\Player\Player;

class GameRecordFactoryEloquent implements GameRecordFactory
{
    /**
     * @throws GameRecordException
     */
    public function create(GameInvite $invite, Player $player, bool $isWinner, array $score): GameRecord
    {
        if (!$inviteModel = GameInviteEloquentModel::where('id', '=', $invite->getId())->first()) {
            throw new GameRecordException(GameRecordException::MESSAGE_MISSING_INVITE);
        }

        try {

            $record = new GameRecordEloquentCoreModel();

            $record->score = json_encode($score);
            $record->winnerFlag = $isWinner;

            $record->gameInvite()->associate($inviteModel);
            $record->playerable()->associate($player);

            $record->save();

            return $record;

        } catch (UniqueConstraintViolationException) {
            throw new GameRecordException(GameRecordException::MESSAGE_DUPLICATE_RECORD);
        }
    }
}
