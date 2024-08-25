<?php

namespace App\Models;

use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnectException;
use App\GameCore\GamePlayDisconnection\GamePlayDisconnection;
use DateTimeImmutable;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use MyDramGames\Utils\Player\Player;

class GamePlayDisconnectionEloquentModel extends Model implements GamePlayDisconnection
{
    use HasFactory;

    protected $table = 'gameplay_disconnects_model';
    public $timestamps = false;

    /**
     * @throws GamePlayDisconnectException
     */
    public function setGamePlay(GamePlay $gamePlay): void
    {
        if (isset($this->gamePlay)) {
            throw new GamePlayDisconnectException(GamePlayDisconnectException::MESSAGE_GAMEPLAY_ALREADY_SET);
        }

        $this->gamePlay()->associate(GamePlayStorageEloquentModel::where('id', '=', $gamePlay->getId())->first());
    }

    /**
     * @throws GamePlayDisconnectException
     */
    public function setPlayer(Player $player): void
    {
        if (isset($this->playerable)) {
            throw new GamePlayDisconnectException(GamePlayDisconnectException::MESSAGE_PLAYER_ALREADY_SET);
        }

        $this->playerable()->associate($player);
    }

    public function setDisconnectedAt(): void
    {
        $this->disconnected_at = new DateTimeImmutable();
    }

    /**
     * @throws GamePlayDisconnectException
     * @throws Exception
     */
    public function hasExpired(DateTimeImmutable|int $expirationTimeInSeconds): bool
    {
        if (!isset($this->disconnected_at)) {
            throw new GamePlayDisconnectException(GamePlayDisconnectException::MESSAGE_TIMESTAMP_NOT_SET);
        }

        $dateTimeBase = is_string($this->disconnected_at)
            ? (new DateTimeImmutable($this->disconnected_at))
            : $this->disconnected_at;

        $expirationTimestamp = $dateTimeBase->modify("+$expirationTimeInSeconds seconds");
        $actualTimestamp = new DateTimeImmutable();

        return $expirationTimestamp < $actualTimestamp;
    }

    /**
     * @throws GamePlayDisconnectException
     */
    public function remove(): void
    {
        if (!isset($this->id)) {
            throw new GamePlayDisconnectException(GamePlayDisconnectException::MESSAGE_DELETING_BEFORE_SAVE);
        }
        $this->delete();
    }

    public function gamePlay(): BelongsTo
    {
        return $this->belongsTo(GamePlayStorageEloquentModel::class);
    }

    public function playerable(): MorphTo
    {
        return $this->morphTo();
    }
}
