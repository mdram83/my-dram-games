<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use MyDramGames\Core\GameInvite\GameInvite;
use MyDramGames\Core\GameInvite\GameInviteRepository;
use MyDramGames\Core\GameRecord\GameRecord;
use MyDramGames\Utils\Player\Player;

class GameRecordEloquentCoreModel extends Model implements GameRecord
{
    use HasFactory;

    protected $table = 'game_record_eloquent_models';
    // TODO after switching to this one, you can remove Core from classname and above $table variable. For now classname without Core is in use.

    public function getPlayer(): Player
    {
        return $this->playerable;
    }

    public function getScore(): array
    {
        return json_decode($this->score, true);
    }

    public function isWinner(): bool
    {
        return (bool) $this->winnerFlag;
    }

    public function gameInvite(): BelongsTo
    {
        return $this->belongsTo(GameInviteEloquentModel::class);
    }

    public function playerable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getGameInvite(): GameInvite
    {
        return app()->make(GameInviteRepository::class)->getOne($this->gameInvite->id);
    }
}
