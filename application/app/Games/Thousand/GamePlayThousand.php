<?php

namespace App\Games\Thousand;

use App\GameCore\GameElements\GameBoard\GameBoardException;
use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuit;
use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayBase;
use App\GameCore\GamePlay\GamePlayException;
use App\GameCore\GamePlay\GamePlayServicesProvider;
use App\GameCore\GamePlayStorage\GamePlayStorage;
use App\GameCore\GameRecord\GameRecordFactory;
use App\GameCore\GameResult\GameResultException;
use App\GameCore\GameResult\GameResultProviderException;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\Collection;
use App\GameCore\Services\Collection\CollectionException;
use App\Games\Thousand\Elements\GamePhaseThousand;
use App\Games\Thousand\Elements\GamePhaseThousandSorting;

class GamePlayThousand extends GamePlayBase implements GamePlay
{
    protected PlayingCardDeckProvider $deckProvider;

    private array $playersData;
    private Player $dealer;
    private Player $obligation;
    private Player $activePlayer;

    private ?Player $bidWinner;
    private int $bidAmount;

    private CollectionPlayingCard $stock;
    private CollectionPlayingCard $table;
    private CollectionPlayingCard $deck;

    private int $round;
    private ?PlayingCardSuit $trumpSuit;
    private GamePhaseThousand $phase;

    //    protected ?GameResultTicTacToe $result = null;

    protected function configureOptionalGamePlayServices(GamePlayServicesProvider $provider): void
    {
        $this->deckProvider = $provider->getPlayingCardDeckProvider();
    }

    /**
     * @throws GamePlayException
     * @throws GameBoardException
     */
    public function handleMove(GameMove $move): void
    {
        if ($this->isFinished()) {
            throw new GamePlayException(GamePlayException::MESSAGE_MOVE_ON_FINISHED_GAME);
        }

//        $this->validateMove($move);

        // handle move/phase
//        $this->setActivePlayer($this->getNextPlayer($move->getPlayer()));
//        $this->saveData();

//        $resultProvider = new GameResultProviderTicTacToe(clone $this->collectionHandler, $this->gameRecordFactory);

//        if ($this->result = $resultProvider->getResult([
//            'board' => $this->board,
//            'characters' => $this->characters,
//            'nextMoveCharacterName' => $this->getPlayerCharacterName($this->activePlayer),
//        ])) {
//            $resultProvider->createGameRecords($this->getGameInvite());
//            $this->storage->setFinished();
//        }
    }

    /**
     * @throws GamePlayException|GameResultProviderException|GameResultException|GameBoardException|CollectionException
     */
    public function handleForfeit(Player $player): void
    {
        if (!$this->getPlayers()->exist($player->getId())) {
            throw new GamePlayException(GamePlayException::MESSAGE_NOT_PLAYER);
        }

        if ($this->isFinished()) {
            throw new GamePlayException(GamePlayException::MESSAGE_MOVE_ON_FINISHED_GAME);
        }

//        $resultProvider = new GameResultProviderTicTacToe(clone $this->collectionHandler, $this->gameRecordFactory);
//
//        $this->result = $resultProvider->getResult(
//            ['forfeitCharacter' => $this->getPlayerCharacterName($player), 'characters' => $this->characters]
//        );
//        $resultProvider->createGameRecords($this->getGameInvite());
//        $this->storage->setFinished();
    }

    /**
     * @throws GamePlayException
     */
    public function getSituation(Player $player): array
    {
        if (!$this->players->exist($player->getId())) {
            throw new GamePlayException(GamePlayException::MESSAGE_NOT_PLAYER);
        }

        $situationData = $this->getSituationData();

        foreach ($situationData['orderedPlayers'] as $playerName => $playerData) {

            if ($playerName !== $player->getName()) {
                $situationData['orderedPlayers'][$playerName]['hand'] = count($situationData['orderedPlayers'][$playerName]['hand']);
            }

            $situationData['orderedPlayers'][$playerName]['tricks'] = count($situationData['orderedPlayers'][$playerName]['tricks']);
        }

        $situationData['stock'] = count($situationData['stock']);

        return $situationData;

    }

    protected function initialize(): void
    {
        $this->initializePlayersData();
        $this->dealer = $this->players->getOne(array_keys($this->playersData)[0]);
        $this->obligation = $this->getNextOrderedPlayer($this->dealer);
        $this->activePlayer = $this->getNextOrderedPlayer($this->obligation);

        $this->bidWinner = null;
        $this->bidAmount = 100;

        $this->stock = $this->getEmptyPlayingCardCollection();
        $this->table = $this->getEmptyPlayingCardCollection();
        $this->deck = $this->deckProvider->getDeckSchnapsen();
        $this->shuffleAndDealCards();

        $this->round = 1;
        $this->trumpSuit = null;
        $this->phase = new GamePhaseThousandSorting();

        // TODO continue here to save data, restore it, and for both cases getSituation for specific player
        // TODO this will be tricky as today I use generic gameplay repository utilizing GamePlayBase constructor...

        // TODO test getSituation for both 3 and 4 players


        // TODO add gameOptions to situation (if not exposed to frontend gameplay-show by Controller

        $this->saveData();
    }

    protected function saveData(): void
    {
        $this->storage->setGameData($this->getSituationData());
    }

    protected function loadData(): void
    {
        $data = $this->storage->getGameData();



//        $this->setActivePlayer($this->players->getOne($data['activePlayerId']));
//        $this->setCharacters(
//            $this->players->getOne($data['characters']['x']),
//            $this->players->getOne($data['characters']['o'])
//        );
//        $this->setBoard($data['board']);
    }

    private function getNextOrderedPlayer(Player $player): Player
    {
        $keys = array_keys($this->playersData);
        $currentPlayerIndex = array_search($player->getId(), $keys, true);
        $nextPlayerIndex = ($currentPlayerIndex + 1) % count($this->playersData);
        $nextPlayerId = $keys[$nextPlayerIndex];

        return $this->players->getOne($nextPlayerId);
    }

    private function initializePlayersData(): void
    {
        $this->playersData = array_fill_keys(array_keys($this->players->shuffle()->toArray()), null);

        foreach (array_keys($this->playersData) as $playerId) {
            $this->playersData[$playerId] = [
                'hand' => $this->getEmptyPlayingCardCollection(),
                'tricks' => $this->getEmptyPlayingCardCollection(),
                'barrel' => false,
                'points' => [],
            ];
        }
    }

    // TODO extract to service
    private function getEmptyPlayingCardCollection(): CollectionPlayingCard
    {
        return new CollectionPlayingCard(clone $this->collectionHandler, []);
    }

    // TODO extract to service
    private function shuffleAndDealCards(): void
    {
        $this->deck->shuffle()->shuffle()->shuffle();

        for ($i = 1; $i <= 3; $i++) {
            $this->stock->add($this->deck->pullFirst());
        }

        reset($this->playersData);

        while ($this->deck->count() > 0) {
            next($this->playersData);
            if (key($this->playersData) === null) {
                reset($this->playersData);
            }

            if ($this->players->count() === 4 && key($this->playersData) === $this->dealer->getId()) {
                continue;
            }

            $this->playersData[key($this->playersData)]['hand']->add($this->deck->pullFirst());
        }
    }

    private function getSituationData(): array
    {
        $orderedPlayers = [];

        foreach ($this->playersData as $playerId => $playerData) {
            $orderedPlayers[$this->players->getOne($playerId)->getName()] = [
                'hand' => $this->getCardsKeys($playerData['hand']),
                'tricks' => $this->getCardsKeys($playerData['tricks']),
                'barrel' => $playerData['barrel'],
                'points' => $playerData['points'],
            ];
        }

        return [
            'orderedPlayers' => $orderedPlayers,
            'stock' => $this->getCardsKeys($this->stock),
            'table' => $this->getCardsKeys($this->table),
            'trumpSuit' => $this->trumpSuit,
            'bidWinner' => $this->bidWinner?->getName(),
            'bidAmount' => $this->bidAmount,
            'activePlayer' => $this->activePlayer->getName(),
            'dealer' => $this->dealer->getName(),
            'obligation' => $this->obligation->getName(),
            'phase' => [
                'key' => $this->phase->getKey(),
                'name' => $this->phase->getName(),
                'description' => $this->phase->getDescription(),
            ],
            'isFinished' => $this->isFinished(),
        ];
    }

    // TODO extract to service
    private function getCardsKeys(CollectionPlayingCard $cards): array
    {
        return array_map(fn($card) => $card->getKey(), $cards->toArray());
    }
}
