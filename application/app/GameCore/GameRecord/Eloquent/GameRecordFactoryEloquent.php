<?php

namespace App\GameCore\GameRecord\Eloquent;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameRecord\GameRecord;
use App\GameCore\GameRecord\GameRecordException;
use App\GameCore\GameRecord\GameRecordFactory;
use App\Models\GameInviteEloquentModel;
use App\Models\GameRecordEloquentModel;
use Illuminate\Database\UniqueConstraintViolationException;
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

            $record = new GameRecordEloquentModel();

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
