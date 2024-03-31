<?php

namespace App\GameCore\GameRecord\Eloquent;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameRecord\CollectionGameRecord;
use App\GameCore\GameRecord\GameRecordRepository;
use App\GameCore\Services\Collection\Collection;
use App\Models\GameRecordEloquentModel;

class GameRecordRepositoryEloquent implements GameRecordRepository
{
    public function __construct(readonly private Collection $handler)
    {

    }

    public function getByGameInvite(GameInvite $gameInvite): CollectionGameRecord
    {
        $records = GameRecordEloquentModel::where('game_invite_id', '=', $gameInvite->getId())->get();
        return new CollectionGameRecord(clone $this->handler, $records->all());
    }
}
