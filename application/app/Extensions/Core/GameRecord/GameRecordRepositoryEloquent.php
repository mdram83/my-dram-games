<?php

namespace App\Extensions\Core\GameRecord;

use App\Models\GameRecordEloquentCoreModel;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameRecord\GameRecordCollection;
use MyDramGames\Core\GameRecord\GameRecordRepository;
use MyDramGames\Utils\Exceptions\CollectionException;

readonly class GameRecordRepositoryEloquent implements GameRecordRepository
{
    public function __construct(private GameRecordCollection $gameRecordCollection)
    {

    }

    /**
     * @throws CollectionException
     */
    public function getByGameInvite(GameInvite $gameInvite): GameRecordCollection
    {
        $records = GameRecordEloquentCoreModel::where('game_invite_id', '=', $gameInvite->getId())->get();
        return $this->gameRecordCollection->clone()->reset($records->all());
    }
}
