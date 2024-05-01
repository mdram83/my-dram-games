<?php

namespace App\Games\Thousand;

use App\GameCore\GameElements\GamePlayPlayers\CollectionGamePlayPlayers;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GameRecord\CollectionGameRecord;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\GameResult\GameResult;
use App\GameCore\GameResult\GameResultException;
use App\GameCore\GameResult\GameResultProvider;
use App\GameCore\GameResult\GameResultProviderException;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;

class GameResultProviderThousand implements GameResultProvider
{
    private bool $resultProvided = false;
    private bool $recordCreated = false;
    private ?Player $winner = null;
    private ?Player $forfeited = null;

    private CollectionGamePlayPlayers $players;
    private array $playersData;

    public function __construct(
        readonly private Collection $handler,
        readonly private GameRecordFactory $recordFactory
    )
    {

    }

    /**
     * @throws GameResultProviderException
     * @throws GameResultException
     */
    public function getResult(mixed $data): ?GameResult
    {
        if ($this->resultProvided) {
            throw new GameResultProviderException(GameResultProviderException::MESSAGE_RESULTS_ALREADY_SET);
        }

        $this->validateData($data);
        $this->players = $data['players'];
        $this->playersData = $data['playersData'];
        $this->forfeited = $data['forfeited'] ?? null;

        $this->resultProvided = true;

        return $this->getForfeitResult() ?? $this->getWinResult();
    }

    /**
     * @throws GameResultProviderException
     */
    public function createGameRecords(GameInvite $gameInvite): CollectionGameRecord
    {
        if (!$this->resultProvided) {
            throw new GameResultProviderException(GameResultProviderException::MESSAGE_RESULT_NOT_SET);
        }

        if ($this->recordCreated) {
            throw new GameResultProviderException(GameResultProviderException::MESSAGE_RECORD_ALREADY_SET);
        }

        $recordsCollection = new CollectionGameRecord(clone $this->handler, []);

        foreach ($this->players->toArray() as $playerId => $player) {
            $record = $this->recordFactory->create(
                $gameInvite,
                $player,
                $player->getId() === $this->winner?->getId(),
                array_merge(
                    ['pointsTable' => $this->playersData[$player->getId()]['points']],
                    !isset($this->forfeited) ? [] : ['forfeit' => $player->getId() === $this->forfeited->getId()]
                )

            );
            $recordsCollection->add($record);
        }

        $this->recordCreated = true;

        return $recordsCollection;
    }

    /**
     * @throws GameResultProviderException
     */
    private function validateData(mixed $data): void
    {
        if (
            !is_array($data)
            || !isset($data['players']) || !is_a($data['players'], CollectionGamePlayPlayers::class)
            || count(array_diff_key($data['players']->toArray(), $data['playersData'])) > 0
            || count(array_diff_key($data['playersData'], $data['players']->toArray())) > 0
            || (isset($data['forfeited']) && !is_a($data['forfeited'], Player::class))
        ) {
            throw new GameResultProviderException(GameResultProviderException::MESSAGE_INCORRECT_DATA_PARAMETER);
        }

        foreach ($data['playersData'] as $playerId => $playerData) {
            if (!isset($playerData['points'])) {
                throw new GameResultProviderException(GameResultProviderException::MESSAGE_INCORRECT_DATA_PARAMETER);
            }
        }
    }

    /**
     * @throws GameResultException
     */
    private function getWinResult(): ?GameResultThousand
    {
        [$points, $resultData] = $this->getLastRoundPointsAndResultData();

        $maxPoints = max($points);

        if ($maxPoints < 1000) {
            return null;
        }

        $winnerPoints = array_filter($points, fn($point) => $point === $maxPoints);
        $winnerId = array_keys($winnerPoints)[0];
        $this->winner = $this->players->getOne($winnerId);

        return new GameResultThousand($resultData, $this->winner);
    }

    /**
     * @throws GameResultException
     */
    private function getForfeitResult(): ?GameResultThousand
    {
        if (isset($this->forfeited)) {
            [$points, $resultData] = $this->getLastRoundPointsAndResultData();
            return new GameResultThousand($resultData, null, $this->forfeited);
        }
        return null;
    }

    private function getLastRoundPointsAndResultData(): array
    {
        $points = [];
        $resultData = [];

        foreach ($this->playersData as $playerId => $playerData) {

            $points[$playerId] =
                count($playerData['points']) > 0
                    ? $playerData['points'][max(array_keys($playerData['points']))]
                    : 0;
            $resultData[$this->playersData[$playerId]['seat']] = [
                'playerName' => $this->players->getOne($playerId)->getName(),
                'points' => $points[$playerId]
            ];
        }

        return [$points, $resultData];
    }
}
