<?php

namespace App\Models;

use App\GameCore\GameRecord\GameRecord;
use App\GameCore\Player\Player;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GameRecordEloquentModel extends Model implements GameRecord
{
    use HasFactory;

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
}
