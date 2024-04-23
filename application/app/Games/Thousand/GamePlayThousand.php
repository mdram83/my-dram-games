<?php

namespace App\Games\Thousand;

use App\GameCore\GameElements\GameBoard\GameBoardException;
use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCardUnique;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealer;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealerException;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuit;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuitRepository;
use App\GameCore\GameElements\GameMove\GameMove;
use App\GameCore\GameElements\GameMove\GameMoveException;
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
use App\Games\Thousand\Elements\GameMoveThousandStockDistribution;
use App\Games\Thousand\Elements\GamePhaseThousandBidding;
use App\Games\Thousand\Elements\GamePhaseThousandRepository;

class GamePlayThousand extends GamePlayBase implements GamePlay
{
    protected PlayingCardDeckProvider $deckProvider;
    protected PlayingCardSuitRepository $suitRepository;
    protected PlayingCardDealer $cardDealer;
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

    private array $marriagesKeys = [
        ['K-H', 'Q-H'],
        ['K-D', 'Q-D'],
        ['K-C', 'Q-C'],
        ['K-S', 'Q-S'],
    ];

    //    protected ?GameResultTicTacToe $result = null;

    /**
     * @throws GamePlayException|GameMoveException|CollectionException
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

        $this->stock = $this->cardDealer->getEmptyStock();
        $this->stockRecord = $this->cardDealer->getEmptyStock();
        $this->table = $this->cardDealer->getEmptyStock();
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

        $this->deck = $this->deckProvider->getDeckSchnapsen();

        foreach ($data['orderedPlayers'] as $playerName => $playerData) {
            $this->playersData[$this->getPlayerByName($playerName)->getId()] = [
                'hand' => $this->cardDealer->getCardsByKeys($this->deck, $playerData['hand'], true, true),
                'tricks' => $this->cardDealer->getCardsByKeys($this->deck, $playerData['tricks'], true, true),
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

        $this->stock = $this->cardDealer->getCardsByKeys($this->deck, $data['stock'], true, true);
        $this->table = $this->cardDealer->getCardsByKeys($this->deck, $data['table'], true, true);
        $this->stockRecord = $this
            ->cardDealer
            ->getCardsByKeys($this->deckProvider->getDeckSchnapsen(), $data['stockRecord'], true, true);

        $this->round = $data['round'];

        $this->trumpSuit = isset($data['trumpSuit']) ? $this->suitRepository->getOne($data['trumpSuit']) : null;
        $this->phase = $this->phaseRepository->getOne($data['phase']['key']);
    }

    protected function configureOptionalGamePlayServices(GamePlayServicesProvider $provider): void
    {
        $this->deckProvider = $provider->getPlayingCardDeckProvider();
        $this->suitRepository = $provider->getPlayingCardSuitRepository();
        $this->cardDealer = $provider->getPlayingCardDealer();
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
     * @throws GameMoveException
     * @throws CollectionException
     */
    private function handleMoveByPhase(GameMove $move): void
    {
        switch ($move::class) {
            case GameMoveThousandBidding::class:
                $this->handleMoveBidding($move);
                break;
            case GameMoveThousandStockDistribution::class:
                $this->handleMoveStockDistribution($move);
                break;
            default:
                throw new GameMoveException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }
    }

    /**
     * @throws GamePlayException
     */
    private function handleMoveSorting(GameMove $move): void
    {
        try {
            $this->cardDealer->getSortedCards(
                $this->playersData[$move->getPlayer()->getId()]['hand'],
                $move->getDetails()['hand'],
                true
            );
        } catch (PlayingCardDealerException) {
            throw new GamePlayException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }
    }

    /**
     * @throws GamePlayThousandException
     * @throws CollectionException
     */
    private function handleMoveBidding(GameMove $move): void
    {
        if ($move->getDetails()['decision'] === 'bid') {

            $bidAmount = $move->getDetails()['bidAmount'];

            if ($bidAmount !== ($this->bidAmount + 10)) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_BID_STEP_INVALID);
            }

            if (
                $bidAmount > 120
                && !$this->cardDealer->hasStockAnyCombination(
                    $this->playersData[$move->getPlayer()->getId()]['hand'],
                    $this->marriagesKeys
                )
            ) {
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
                $this->stockRecord = $this->stockRecord->reset($this->stock->toArray());
            }

            $this->cardDealer->collectCards($this->playersData[$this->bidWinner->getId()]['hand'], [$this->stock]);

            foreach ($this->playersData as $playerId => $playerData) {
                $this->playersData[$playerId]['bid'] = null;
            }

        } else {
            $this->phase = $this->phase->getNextPhase(false);
            $this->activePlayer = $this->getNextOrderedPlayer($move->getPlayer());
        }
    }

    /**
     * @throws GamePlayThousandException
     */
    private function handleMoveStockDistribution(GameMove $move): void
    {
        $distribution = $move->getDetails()['distribution'];
        $this->validateMoveStockDistribution($distribution);

        try {
            foreach ($distribution as $distributionPlayerName => $distributionCardKey) {
                $this->cardDealer->moveCardsByKeys(
                    $this->playersData[$move->getPlayer()->getId()]['hand'],
                    $this->playersData[$this->getPlayerByName($distributionPlayerName)->getId()]['hand'],
                    [$distributionCardKey]
                );
            }
        } catch (PlayingCardDealerException) {
            throw new GamePlayThousandException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }

        $this->phase = $this->phase->getNextPhase(true);
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

    /**
     * @throws GamePlayThousandException
     */
    private function validateMoveStockDistribution(array $distribution): void
    {
        $numberOfPlayers = $this->getGameInvite()->getGameSetup()->getNumberOfPlayers()->getConfiguredValue()->getValue();

        if (
            in_array($this->activePlayer->getName(), array_keys($distribution))
            || count(array_unique($distribution)) !== 2
            || ($numberOfPlayers === 4 && in_array($this->dealer->getName(), array_keys($distribution)))
        ) {
            throw new GamePlayThousandException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }
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
                'hand' => $this->cardDealer->getEmptyStock(),
                'tricks' => $this->cardDealer->getEmptyStock(),
                'barrel' => false,
                'points' => [],
                'ready' => true,
                'bid' => null,
                'seat' => $seat,
            ];
            $seat++;
        }
    }

    private function shuffleAndDealCards(): void
    {
        $definition = [['stock' => $this->stock, 'numberOfCards' => 3]];
        $nextPlayer = $this->dealer;

        for ($i = 1; $i <= 3; $i++) {
            $nextPlayer = $this->getNextOrderedPlayer($nextPlayer);
            $definition[] = ['stock' => $this->playersData[$nextPlayer->getId()]['hand'], 'numberOfCards' => 7];
        }

        $this->cardDealer->shuffleAndDealCards($this->deck, $definition);
    }

    private function getSituationData(): array
    {
        $orderedPlayers = [];

        foreach ($this->playersData as $playerId => $playerData) {
            $orderedPlayers[$this->players->getOne($playerId)->getName()] = [
                'hand' => $this->cardDealer->getCardsKeys($playerData['hand']),
                'tricks' => $this->cardDealer->getCardsKeys($playerData['tricks']),
                'barrel' => $playerData['barrel'],
                'points' => $playerData['points'],
                'ready' => $playerData['ready'],
                'bid' => $playerData['bid'],
                'seat' => $playerData['seat'],
            ];
        }

        return [
            'orderedPlayers' => $orderedPlayers,
            'stock' => $this->cardDealer->getCardsKeys($this->stock),
            'stockRecord' => $this->cardDealer->getCardsKeys($this->stockRecord),
            'table' => $this->cardDealer->getCardsKeys($this->table),
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
