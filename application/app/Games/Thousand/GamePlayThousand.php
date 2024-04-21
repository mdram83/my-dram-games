<?php

namespace App\Games\Thousand;

use App\GameCore\GameElements\GameBoard\GameBoardException;
use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCardUnique;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuit;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuitRepository;
use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GamePhase\GamePhase;
use App\GameCore\GameElements\GamePhase\GamePhaseException;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\GamePlay\GamePlayBase;
use App\GameCore\GamePlay\GamePlayException;
use App\GameCore\GamePlay\GamePlayServicesProvider;
use App\GameCore\GameResult\GameResultException;
use App\GameCore\GameResult\GameResultProviderException;
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\CollectionException;
use App\Games\Thousand\Elements\GameMoveThousand;
use App\Games\Thousand\Elements\GameMoveThousandBidding;
use App\Games\Thousand\Elements\GameMoveThousandSorting;
use App\Games\Thousand\Elements\GamePhaseThousandBidding;
use App\Games\Thousand\Elements\GamePhaseThousandRepository;

class GamePlayThousand extends GamePlayBase implements GamePlay
{
    protected PlayingCardDeckProvider $deckProvider;
    protected PlayingCardSuitRepository $suitRepository;
    protected GamePhaseThousandRepository $phaseRepository;

    protected const GAME_MOVE_CLASS = GameMoveThousand::class;

    private array $playersData;
    private Player $dealer;
    private Player $obligation;

    private ?Player $bidWinner;
    private int $bidAmount;

    private CollectionPlayingCardUnique $stock;
    private CollectionPlayingCardUnique $stockRecord;
    private CollectionPlayingCardUnique $table;
    private CollectionPlayingCardUnique $deck;

    private int $round;
    private ?PlayingCardSuit $trumpSuit;
    private GamePhase $phase;

    //    protected ?GameResultTicTacToe $result = null;

    /**
     * @throws GamePlayException
     * @throws GameBoardException
     */
    public function handleMove(GameMove $move): void
    {
        if ($this->isFinished()) {
            throw new GamePlayException(GamePlayException::MESSAGE_MOVE_ON_FINISHED_GAME);
        }

        if (is_a($move, GameMoveThousandSorting::class)) {
            $this->handleMoveSorting($move);
            return;
        }

        $this->validateMove($move);
        $this->handleMoveByPhase($move);
        $this->saveData();

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
        $this->playersData[$this->obligation->getId()]['bid'] = $this->bidAmount;

        $this->stock = $this->getEmptyPlayingCardCollection();
        $this->stockRecord = $this->getEmptyPlayingCardCollection();
        $this->table = $this->getEmptyPlayingCardCollection();
        $this->deck = $this->deckProvider->getDeckSchnapsen();
        $this->shuffleAndDealCards();

        $this->round = 1;
        $this->trumpSuit = null;
        $this->phase = new GamePhaseThousandBidding();

        $this->saveData();
    }

    protected function saveData(): void
    {
        $this->storage->setGameData($this->getSituationData());
    }

    /**
     * @throws GamePhaseException
     */
    protected function loadData(): void
    {
        $data = $this->storage->getGameData();

        foreach ($data['orderedPlayers'] as $playerName => $playerData) {
            $this->playersData[$this->getPlayerByName($playerName)->getId()] = [
                'hand' => $this->getCardsByKeys($playerData['hand']),
                'tricks' => $this->getCardsByKeys($playerData['tricks']),
                'barrel' => $playerData['barrel'],
                'points' => $playerData['points'],
                'ready' => $playerData['ready'],
                'bid' => $playerData['bid'],
                'seat' => $playerData['seat'],
            ];
        }

        $this->dealer = $this->getPlayerByName($data['dealer']);
        $this->obligation = $this->getPlayerByName($data['obligation']);
        $this->activePlayer = $this->getPlayerByName($data['activePlayer']);

        $this->bidWinner = $this->getPlayerByName($data['bidWinner']);
        $this->bidAmount = $data['bidAmount'];

        $this->stock = $this->getCardsByKeys($data['stock']);
        $this->stockRecord = $this->getCardsByKeys($data['stockRecord']);
        $this->table = $this->getCardsByKeys($data['table']);
        $this->deck = $this->getEmptyPlayingCardCollection();

        $this->round = $data['round'];

        $this->trumpSuit = isset($data['trumpSuit']) ? $this->suitRepository->getOne($data['trumpSuit']) : null;
        $this->phase = $this->phaseRepository->getOne($data['phase']['key']);
    }

    protected function configureOptionalGamePlayServices(GamePlayServicesProvider $provider): void
    {
        $this->deckProvider = $provider->getPlayingCardDeckProvider();
        $this->suitRepository = $provider->getPlayingCardSuitRepository();
        $this->phaseRepository = new GamePhaseThousandRepository();
    }

    /**
     * @throws GamePlayException
     */

    protected function validateMove(GameMove $move): void
    {
        parent::validateMove($move);

        if ($this->phase->getKey() !== $move->getDetails()['phase']->getKey()) {
            throw new GamePlayException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }
    }

    /**
     * @throws GamePlayThousandException
     */
    private function handleMoveByPhase(GameMove $move): void
    {
        switch ($move::class) {
            case GameMoveThousandBidding::class:
                $this->handleMoveBidding($move);
                break;
        }
    }

    /**
     * @throws GamePlayException
     */
    private function handleMoveSorting(GameMove $move): void
    {
        $orderedHandKeys = $move->getDetails()['hand'];
        $currentHandKeys = array_keys($this->playersData[$move->getPlayer()->getId()]['hand']->toArray());

        if (count(array_diff($currentHandKeys, $orderedHandKeys)) > 0) {
            throw new GamePlayException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }

        $this->playersData[$move->getPlayer()->getId()]['hand'] = $this->getCardsByKeys($orderedHandKeys);
    }

    /**
     * @throws GamePlayThousandException
     */
    private function handleMoveBidding(GameMove $move): void
    {
        if ($move->getDetails()['decision'] === 'bid') {

            $bidAmount = $move->getDetails()['bidAmount'];

            if ($bidAmount !== ($this->bidAmount + 10)) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_BID_STEP_INVALID);
            }

            if ($bidAmount > 120 && !$this->hasMarriage($this->playersData[$move->getPlayer()->getId()]['hand'])) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_BID_NO_MARRIAGE);
            }

            $this->bidAmount = $bidAmount;
        }

        $this->playersData[$move->getPlayer()->getId()]['bid'] = $move->getDetails()['bidAmount'] ?? 'pass';


        if ($this->isLastBiddingMove()) {

            $this->phase = $this->phase->getNextPhase(true);
            $this->activePlayer = $this->getHighestBiddingPlayer();
            $this->bidWinner = $this->getHighestBiddingPlayer();

            if ($this->bidAmount > 100) {
                $this->stockRecord = $this->getCardsByKeys($this->getCardsKeys($this->stock));
            }

            while ($this->stock->count() > 0) {
                $this->playersData[$this->bidWinner->getId()]['hand']->add($this->stock->pullFirst());
            }

            foreach ($this->playersData as $playerId => $playerData) {
                $this->playersData[$playerId]['bid'] = null;
            }

        } else {
            $this->phase = $this->phase->getNextPhase(false);
            $this->activePlayer = $this->getNextOrderedPlayer($move->getPlayer());
        }

    }

    private function isLastBiddingMove(): bool
    {
        return
            $this->bidAmount === 300
            || count(array_filter($this->playersData, fn($playerData) => $playerData['bid'] === 'pass')) === 2;
    }

    private function getHighestBiddingPlayer(): Player
    {
        $bids = array_filter(
            array_map(fn($playerData) => $playerData['bid'], $this->playersData),
            fn($bid) => ($bid !== null && $bid !== 'pass')
        );

        return $this->players->getOne(array_search(max($bids), $bids));
    }

    private function getNextOrderedPlayer(Player $player): Player
    {
        $numberOfPlayers = $this->getGameInvite()->getGameSetup()->getNumberOfPlayers()->getConfiguredValue()->getValue();
        $currentPlayerSeat = $this->playersData[$player->getId()]['seat'];
        $nextPlayerSeat = ($currentPlayerSeat % $numberOfPlayers) + 1;
        $nextPlayerId = array_keys(array_filter(
            $this->playersData,
            fn($playerData) => $playerData['seat'] === $nextPlayerSeat
        ))[0];

        $nextPlayer = $this->players->getOne($nextPlayerId);

        return
            (
                $this->playersData[$nextPlayerId]['bid'] === 'pass'
                || ($numberOfPlayers === 4 && $nextPlayer->getId() === $this->dealer->getId())
            )
                ? $this->getNextOrderedPlayer($nextPlayer)
                : $nextPlayer;
    }

    private function getPlayerByName(?string $playerName): ?Player
    {
        if ($playerName === null) {
            return null;
        }

        $playerId = array_keys(array_filter(
            $this->players->toArray(),
            fn($item) => $item->getName() === $playerName
        ))[0];

        return $this->players->getOne($playerId);
    }

    private function initializePlayersData(): void
    {
        $this->playersData = array_fill_keys(array_keys($this->players->shuffle()->toArray()), null);

        $seat = 1;
        foreach (array_keys($this->playersData) as $playerId) {
            $this->playersData[$playerId] = [
                'hand' => $this->getEmptyPlayingCardCollection(),
                'tricks' => $this->getEmptyPlayingCardCollection(),
                'barrel' => false,
                'points' => [],
                'ready' => true,
                'bid' => null,
                'seat' => $seat,
            ];
            $seat++;
        }
    }

    // TODO extract to service
    private function getEmptyPlayingCardCollection(): CollectionPlayingCardUnique
    {
        return new CollectionPlayingCardUnique(clone $this->collectionHandler, []);
    }

    // TODO extract to service
    private function getCardsByKeys(?array $keys): CollectionPlayingCardUnique
    {
        if ($keys === null || $keys === []) {
            return $this->getEmptyPlayingCardCollection();
        }

        $deck = $this->deckProvider->getDeckSchnapsen();

        return $this->getEmptyPlayingCardCollection()->reset(array_map(fn($cardKey) => $deck->getOne($cardKey), $keys));

    }

    // TODO extract to service
    private function getCardsKeys(CollectionPlayingCardUnique $cards): array
    {
        return array_keys($cards->toArray());
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

    // TODO extract to service
    private function hasMarriage(CollectionPlayingCardUnique $cards): bool
    {
        $cardKeys = $this->getCardsKeys($cards);
        $marriagesKeys = [
            ['K-H', 'Q-H'],
            ['K-D', 'Q-D'],
            ['K-C', 'Q-C'],
            ['K-S', 'Q-S'],
        ];

        foreach ($marriagesKeys as $marriageKeys) {
            if (in_array($marriageKeys[0], $cardKeys) && in_array($marriageKeys[1], $cardKeys)) {
                return true;
            }
        }

        return false;
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
                'ready' => $playerData['ready'],
                'bid' => $playerData['bid'],
                'seat' => $playerData['seat'],
            ];
        }

        return [
            'orderedPlayers' => $orderedPlayers,
            'stock' => $this->getCardsKeys($this->stock),
            'stockRecord' => $this->getCardsKeys($this->stockRecord),
            'table' => $this->getCardsKeys($this->table),
            'trumpSuit' => $this->trumpSuit?->getKey(),
            'bidWinner' => $this->bidWinner?->getName(),
            'bidAmount' => $this->bidAmount,
            'activePlayer' => $this->activePlayer->getName(),
            'dealer' => $this->dealer->getName(),
            'obligation' => $this->obligation->getName(),
            'round' => $this->round,
            'phase' => [
                'key' => $this->phase->getKey(),
                'name' => $this->phase->getName(),
                'description' => $this->phase->getDescription(),
            ],
            'isFinished' => $this->isFinished(),
        ];
    }
}
