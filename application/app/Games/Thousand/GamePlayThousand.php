<?php

namespace App\Games\Thousand;

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
use App\GameCore\Services\Collection\CollectionException;
use App\Games\Thousand\Elements\GameMoveThousand;
use App\Games\Thousand\Elements\GameMoveThousandBidding;
use App\Games\Thousand\Elements\GameMoveThousandCollectTricks;
use App\Games\Thousand\Elements\GameMoveThousandCountPoints;
use App\Games\Thousand\Elements\GameMoveThousandDeclaration;
use App\Games\Thousand\Elements\GameMoveThousandPlayCard;
use App\Games\Thousand\Elements\GameMoveThousandSorting;
use App\Games\Thousand\Elements\GameMoveThousandStockDistribution;
use App\Games\Thousand\Elements\GamePhaseThousandBidding;
use App\Games\Thousand\Elements\GamePhaseThousandCountPoints;
use App\Games\Thousand\Elements\GamePhaseThousandRepository;
use App\Games\Thousand\Tools\CollectionPlayerDataThousand;
use App\Games\Thousand\Tools\GameDataThousand;
use App\Games\Thousand\Tools\GameStewardThousand;
use App\Games\Thousand\Tools\PlayerDataThousand;
use MyDramGames\Utils\Player\Player;

class GamePlayThousand extends GamePlayBase implements GamePlay
{
    protected const GAME_MOVE_CLASS = GameMoveThousand::class;

    protected PlayingCardDeckProvider $deckProvider;
    protected PlayingCardSuitRepository $suitRepository;
    protected PlayingCardDealer $cardDealer;
    protected GamePhaseThousandRepository $phaseRepository;
    protected GameStewardThousand $steward;

    protected ?GameResultThousand $result = null;
    private GameDataThousand $gameData;
    private CollectionPlayerDataThousand $playersData;

    /**
     * @throws GamePlayException|GameMoveException|CollectionException|GameResultProviderException|GameResultException
     */
    public function handleMove(GameMove $move): void
    {
        $this->validateActionOnFinishedGame();

        switch ($move::class) {

            case GameMoveThousandSorting::class:
                $this->handleMoveSorting($move);
                break;

            case GameMoveThousandCountPoints::class:
                $this->handleMoveCountPoints($move);
                break;

            default:
                $this->validateMove($move);
                $this->handleMoveByPhase($move);
        }

        $this->saveData();
        $this->checkAndSetWinResult();
    }

    /**
     * @throws GameResultException|GameResultProviderException
     */
    private function checkAndSetWinResult(): void
    {
        if ($this->gameData->round > 1) {

            $resultProvider = new GameResultProviderThousand(clone $this->collectionHandler, $this->gameRecordFactory);

            if ($this->result = $resultProvider->getResult([
                'players' => $this->players,
                'playersData' => array_map(fn($playerData) => $playerData->toArray(), $this->playersData->toArray())
            ])) {
                $resultProvider->createGameRecords($this->getGameInvite());
                $this->storage->setFinished();
            }
        }
    }

    /**
     * @throws GamePlayException|GameResultProviderException|GameResultException
     */
    public function handleForfeit(Player $player): void
    {
        $this->validateGamePlayer($player);
        $this->validateActionOnFinishedGame();
        $this->setForfeitResult($player);
    }

    /**
     * @throws GameResultException|GameResultProviderException
     */
    private function setForfeitResult(Player $player): void
    {
        $resultProvider = new GameResultProviderThousand(clone $this->collectionHandler, $this->gameRecordFactory);

        $this->result = $resultProvider->getResult([
            'players' => $this->players,
            'playersData' => array_map(fn($playerData) => $playerData->toArray(), $this->playersData->toArray()),
            'forfeited' => $player,
        ]);

        $resultProvider->createGameRecords($this->getGameInvite());
        $this->storage->setFinished();
    }

    /**
     * @throws GamePlayException
     */
    public function getSituation(Player $player): array
    {
        $this->validateGamePlayer($player);

        $data = $this->getSituationData();

        foreach ($data['orderedPlayers'] as $playerName => $playerData) {
            if ($playerName !== $player->getName()) {
                $data['orderedPlayers'][$playerName]['hand'] = count($data['orderedPlayers'][$playerName]['hand']);
            }
            $data['orderedPlayers'][$playerName]['tricks'] = count($data['orderedPlayers'][$playerName]['tricks']);
        }

        $data['stock'] = count($data['stock']);
        $data['stockRecord'] = (
            $this->gameData->bidAmount > 100
            || ($this->gameData->phase->getKey() === (new GamePhaseThousandCountPoints())->getKey()) && $this->players->count() === 4)
                ? $data['stockRecord']
                : [];

        if (isset($this->result)) {
            $data['result'] = $this->result->toArray();
        }

        return $data;
    }

    private function getSituationData(): array
    {
        $orderedPlayers = [];

        foreach ($this->playersData->toArray() as $playerId => $playerData) {
            $orderedPlayers[$this->players->getOne($playerId)->getName()] = [
                'hand' => $this->cardDealer->getCardsKeys($playerData->hand),
                'tricks' => $this->cardDealer->getCardsKeys($playerData->tricks),
                'barrel' => $playerData->barrel,
                'points' => $playerData->points,
                'ready' => $playerData->ready,
                'bid' => $playerData->bid,
                'seat' => $playerData->seat,
                'bombRounds' => $playerData->bombRounds,
                'trumps' => $playerData->trumps,
            ];
        }

        return [
            'orderedPlayers' => $orderedPlayers,

            'stock' => $this->cardDealer->getCardsKeys($this->gameData->stock),
            'stockRecord' => $this->cardDealer->getCardsKeys($this->gameData->stockRecord),
            'table' => $this->cardDealer->getCardsKeys($this->gameData->table),
            'trumpSuit' => $this->gameData->trumpSuit?->getKey(),
            'turnSuit' => $this->gameData->turnSuit?->getKey(),
            'turnLead' => $this->gameData->turnLead?->getName(),
            'bidWinner' => $this->gameData->bidWinner?->getName(),
            'bidAmount' => $this->gameData->bidAmount,
            'activePlayer' => $this->activePlayer->getName(),
            'dealer' => $this->gameData->dealer->getName(),
            'obligation' => $this->gameData->obligation->getName(),
            'round' => $this->gameData->round,
            'phase' => [
                'key' => $this->gameData->phase->getKey(),
                'name' => $this->gameData->phase->getName(),
                'description' => $this->gameData->phase->getDescription(),
            ],
            'isFinished' => $this->isFinished(),
        ];
    }

    /**
     * @throws CollectionException
     */
    protected function initialize(): void
    {
        $this->initializePlayersData();

        $this->gameData = new GameDataThousand();
        $this->gameData->dealer = $this->players->getOne(array_keys($this->playersData->toArray())[0]);
        $this->setRoundStartParameters();

        $this->gameData->stock = $this->cardDealer->getEmptyStock();
        $this->gameData->stockRecord = $this->cardDealer->getEmptyStock();
        $this->gameData->table = $this->cardDealer->getEmptyStock();
        $this->gameData->deck = $this->deckProvider->getDeckSchnapsen();

        $this->steward->shuffleAndDealCards($this->gameData, $this->playersData);

        $this->gameData->round = 1;
        $this->gameData->trumpSuit = null;
        $this->gameData->turnSuit = null;
        $this->gameData->turnLead = null;
        $this->gameData->phase = new GamePhaseThousandBidding();

        $this->saveData();
    }

    /**
     * @throws CollectionException
     */
    private function initializePlayersData(): void
    {
        $seat = 1;
        $this->playersData = new CollectionPlayerDataThousand(clone $this->collectionHandler, []);

        foreach ($this->players->shuffle()->toArray() as $player) {
            $playerData = new PlayerDataThousand($player);
            $playerData->hand = $this->cardDealer->getEmptyStock();
            $playerData->tricks = $this->cardDealer->getEmptyStock();
            $playerData->seat = $seat;
            $this->playersData->add($playerData);
            $seat++;
        }
    }

    private function setRoundStartParameters(): void
    {
        $this->gameData->obligation = $this->steward->getNextOrderedPlayer($this->gameData->dealer, $this->gameData->dealer, $this->playersData);
        $this->activePlayer = $this->steward->getNextOrderedPlayer($this->gameData->obligation, $this->gameData->dealer, $this->playersData);

        $this->gameData->bidWinner = null;
        $this->gameData->bidAmount = 100;
        $this->playersData->getFor($this->gameData->obligation)->bid = $this->gameData->bidAmount;
    }

    protected function saveData(): void
    {
        $this->storage->setGameData($this->getSituationData());
    }

    /**
     * @throws GamePhaseException
     * @throws CollectionException
     */
    protected function loadData(): void
    {
        $data = $this->storage->getGameData();
        $this->gameData = new GameDataThousand();
        $this->gameData->deck = $this->deckProvider->getDeckSchnapsen();

        $this->playersData = new CollectionPlayerDataThousand(clone $this->collectionHandler, []);
        foreach ($data['orderedPlayers'] as $playerName => $dataPlayer) {
            $playerData = new PlayerDataThousand($this->getPlayerByName($playerName));
            $playerData->hand = $this->cardDealer->getCardsByKeys($this->gameData->deck, $dataPlayer['hand'], true, true);
            $playerData->tricks = $this->cardDealer->getCardsByKeys($this->gameData->deck, $dataPlayer['tricks'], true, true);
            $playerData->barrel = $dataPlayer['barrel'];
            $playerData->points = $dataPlayer['points'];
            $playerData->ready = $dataPlayer['ready'];
            $playerData->bid = $dataPlayer['bid'];
            $playerData->seat = $dataPlayer['seat'];
            $playerData->bombRounds = $dataPlayer['bombRounds'];
            $playerData->trumps = $dataPlayer['trumps'];
            $this->playersData->add($playerData);
        }

        $this->activePlayer = $this->getPlayerByName($data['activePlayer']);

        $this->gameData->dealer = $this->getPlayerByName($data['dealer']);
        $this->gameData->obligation = $this->getPlayerByName($data['obligation']);
        $this->gameData->bidWinner = $this->getPlayerByName($data['bidWinner']);
        $this->gameData->bidAmount = $data['bidAmount'];
        $this->gameData->stock = $this->cardDealer->getCardsByKeys($this->gameData->deck, $data['stock'], true, true);
        $this->gameData->table = $this->cardDealer->getCardsByKeys($this->gameData->deck, $data['table'], true, true);
        $this->gameData->stockRecord = $this->cardDealer->getCardsByKeys($this->gameData->deck, $data['stockRecord'], true, true);
        $this->gameData->round = $data['round'];
        $this->gameData->trumpSuit = isset($data['trumpSuit']) ? $this->suitRepository->getOne($data['trumpSuit']) : null;
        $this->gameData->turnSuit = isset($data['turnSuit']) ? $this->suitRepository->getOne($data['turnSuit']) : null;
        $this->gameData->turnLead = $this->getPlayerByName($data['turnLead']);
        $this->gameData->phase = $this->phaseRepository->getOne($data['phase']['key']);
    }

    protected function configureOptionalGamePlayServices(GamePlayServicesProvider $provider): void
    {
        $this->deckProvider = $provider->getPlayingCardDeckProvider();
        $this->suitRepository = $provider->getPlayingCardSuitRepository();
        $this->cardDealer = $provider->getPlayingCardDealer();
        $this->phaseRepository = new GamePhaseThousandRepository();
    }

    protected function configureServicesAfterHooks(): void
    {
        $this->steward = new GameStewardThousand($this->players, $this->getGameInvite(), $this->cardDealer);
    }

    /**
     * @throws GamePlayException
     */
    protected function validateMove(GameMove $move): void
    {
        parent::validateMove($move);

        if ($this->gameData->phase->getKey() !== $move->getDetails()['phase']->getKey()) {
            throw new GamePlayException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }
    }

    /**
     * @throws GamePlayThousandException|GameMoveException|CollectionException|GamePlayException
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

            case GameMoveThousandDeclaration::class;
                $this->handleMoveDeclaration($move);
                break;

            case GameMoveThousandPlayCard::class:
                $this->handleMovePlayCard($move);
                break;

            case GameMoveThousandCollectTricks::class:
                $this->handleMoveCollectTricks($move);
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
                $this->playersData->getFor($move->getPlayer())->hand,
                $move->getDetails()['hand'],
                true
            );
        } catch (PlayingCardDealerException) {
            throw new GamePlayException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }
    }

    /**
     * @throws GamePlayThousandException|CollectionException
     */
    private function handleMoveBidding(GameMove $move): void
    {
        if ($move->getDetails()['decision'] === 'bid') {

            $newBidAmount = $move->getDetails()['bidAmount'];

            if ($newBidAmount !== ($this->gameData->bidAmount + 10)) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_BID_STEP_INVALID);
            }

            if (
                $newBidAmount > 120
                && !$this->steward->hasMarriageAtHand($this->playersData->getFor($move->getPlayer())->hand)
            ) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_BID_NO_MARRIAGE);
            }

            $this->gameData->bidAmount = $newBidAmount;
        }

        $this->playersData->getFor($move->getPlayer())->bid = $move->getDetails()['bidAmount'] ?? 'pass';

        if ($this->steward->isLastBiddingMove($this->gameData->bidAmount, $this->playersData)) {

            $this->gameData->advanceGamePhase(true);

            $this->activePlayer = $this->steward->getHighestBiddingPlayer($this->playersData);
            $this->gameData->bidWinner = $this->activePlayer;
            $this->gameData->stockRecord = $this->gameData->stockRecord->reset($this->gameData->stock->toArray());

            $this->cardDealer->collectCards($this->playersData->getFor($this->gameData->bidWinner)->hand, [$this->gameData->stock]);

            foreach ($this->playersData->toArray() as $playerData) {
                $playerData->bid = null;
            }

        } else {
            $this->gameData->advanceGamePhase(false);
            $this->activePlayer = $this->steward->getNextOrderedPlayer($move->getPlayer(), $this->gameData->dealer, $this->playersData);
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
                    $this->playersData->getFor($move->getPlayer())->hand,
                    $this->playersData->getFor($this->getPlayerByName($distributionPlayerName))->hand,
                    [$distributionCardKey]
                );
            }
        } catch (PlayingCardDealerException) {
            throw new GamePlayThousandException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }

        $this->gameData->advanceGamePhase(true);
    }

    /**
     * @throws GamePlayThousandException
     */
    private function handleMoveDeclaration(GameMove $move): void
    {
        $declaration = $move->getDetails()['declaration'];
        $this->validateMoveDeclaration($declaration);

        if ($declaration === 0) {

            foreach ($this->players->toArray() as $player) {
                $this->steward->setRoundPoints($player, $this->activePlayer, $this->gameData, $this->playersData, true);
                $this->steward->setBarrelStatus($this->playersData->getFor($player));

                $this->playersData->getFor($player)->hand = $this->cardDealer->getEmptyStock();
                $this->playersData->getFor($player)->ready = false;
            }

            $this->playersData->getFor($this->activePlayer)->bombRounds[] = $this->gameData->round;
            $this->gameData->phase = new GamePhaseThousandCountPoints();

            return;
        }

        $this->gameData->bidAmount = $declaration;
        $this->gameData->turnLead = $this->gameData->bidWinner;
        $this->gameData->advanceGamePhase(true);
    }

    /**
     * @throws GamePlayException
     */
    private function handleMovePlayCard(GameMove $move): void
    {
        $cardKey = $move->getDetails()['card'];
        $hand = $this->playersData->getFor($move->getPlayer())->hand;
        $hasMarriageRequest = $move->getDetails()['marriage'] ?? false;

        $this->validateMovePlayingCard($hand, $cardKey, $hasMarriageRequest);
        $this->cardDealer->moveCardsByKeys($hand, $this->gameData->table, [$cardKey]);

        if ($this->steward->isFirstCardPhase($this->gameData->phase)) {

            $this->gameData->turnSuit = $this->gameData->table->getOne($cardKey)->getSuit();

            if ($hasMarriageRequest) {
                $this->gameData->trumpSuit = $this->gameData->turnSuit;
                $this->playersData->getFor($this->activePlayer)->trumps[] = $cardKey;
            }
        }

        $this->activePlayer = $this->steward->isThirdCardPhase($this->gameData->phase)
            ? $this->steward->getTrickWinner($this->gameData, $this->playersData)
            : $this->steward->getNextOrderedPlayer($move->getPlayer(), $this->gameData->dealer, $this->playersData);

        $this->gameData->advanceGamePhase(true);
    }

    private function handleMoveCollectTricks(GameMove $move): void
    {
        $trickWinner = $move->getPlayer();

        $this->cardDealer->moveCardsTimes(
            $this->gameData->table,
            $this->playersData->getFor($trickWinner)->tricks,
            3
        );

        $this->gameData->turnLead = $trickWinner;
        $this->gameData->turnSuit = null;

        $hand = $this->playersData->getFor($trickWinner)->hand;

        if ($hand->count() === 0) {

            foreach ($this->players->toArray() as $player) {

                $this->steward->setRoundPoints($player, $this->activePlayer, $this->gameData, $this->playersData);
                $this->steward->setBarrelStatus($this->playersData->getFor($player));

                $this->playersData->getFor($player)->tricks = $this->cardDealer->getEmptyStock();
                $this->playersData->getFor($player)->trumps = [];
                $this->playersData->getFor($player)->ready = false;
            }

            $this->gameData->trumpSuit = null;
            $this->gameData->turnLead = null;
            $this->gameData->stockRecord = $this->cardDealer->getEmptyStock();
        }

        $this->gameData->advanceGamePhase($hand->count() === 0);
    }

    /**
     * @throws GamePlayException
     */
    private function handleMoveCountPoints(GameMove $move): void
    {
        if ($this->arePlayersReady($move->getPlayer())) {
            throw new GamePlayException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }

        $this->playersData->getFor($move->getPlayer())->ready = true;

        if ($this->arePlayersReady()) {

            $this->gameData->dealer = $this->steward->getNextOrderedPlayer($this->gameData->dealer, $this->gameData->dealer, $this->playersData);
            $this->setRoundStartParameters();

            $this->steward->shuffleAndDealCards($this->gameData, $this->playersData);

            $this->gameData->round++;
            $this->gameData->advanceGamePhase(true);
        }
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
            || ($numberOfPlayers === 4 && in_array($this->gameData->dealer->getName(), array_keys($distribution)))
        ) {
            throw new GamePlayThousandException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }
    }

    /**
     * @throws GamePlayThousandException
     */
    private function validateMoveDeclaration(int $declaration): void
    {
        if ($declaration === 0) {

            if ($this->gameData->bidAmount !== 100) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_BOMB_ON_BID);
            }

            if ($this->steward->hasPlayerUsedMaxBombMoves($this->playersData->getFor($this->activePlayer)->bombRounds)) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_BOMB_USED);
            }

        } elseif (($declaration < $this->gameData->bidAmount || $declaration > 300 || $declaration % 10 !== 0)) {
            throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_WRONG_DECLARATION);
        }
    }

    /**
     * @throws GamePlayException
     */
    private function validateMovePlayingCard(CollectionPlayingCard $hand, string $cardKey, bool $marriage = false): void
    {
        if (!$hand->exist($cardKey)) {
            throw new GamePlayException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }

        if ($marriage) {
            $marriageCards = $hand->filter(fn($item, $key) => (
                $item->getSuit() === $hand->getOne($cardKey)->getSuit()
                && in_array($item->getRank()->getKey(), ['K', 'Q'])
            ));

            if ($marriageCards->count() !== 2) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_PLAY_TRUMP_PAIR);
            }

            if (!in_array($hand->getOne($cardKey)->getRank()->getKey(), ['K', 'Q'])) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_PLAY_TRUMP_RANK);
            }

            if (!$this->steward->isFirstCardPhase($this->gameData->phase)) {
                throw new GamePlayException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
            }
        }

        if (!$this->steward->isFirstCardPhase($this->gameData->phase)) {
            if (
                $hand->filter(fn($item, $key) => $item->getSuit() === $this->gameData->turnSuit)->count() > 0
                && $hand->getOne($cardKey)->getSuit() !== $this->gameData->turnSuit
            ) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_PLAY_TURN_SUIT);
            }
        }

        if ($this->steward->isSecondCardPhase($this->gameData->phase)) {

            $tableCard = $this->gameData->table->getOne($this->cardDealer->getCardsKeys($this->gameData->table)[0]);
            $higherRankCardsAtHand = $hand->filter(fn($item, $key) => (
                $this->steward->getCardPoints($item) > $this->steward->getCardPoints($tableCard)
                && $item->getSuit() === $this->gameData->turnSuit
            ));

            if ($higherRankCardsAtHand->count() > 0 && !$higherRankCardsAtHand->exist($cardKey)) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_PLAY_HIGH_RANK);
            }
        }
    }

    private function arePlayersReady(?Player $player = null): bool
    {
        $readyPlayersData = $this->playersData->filter(fn($playerData, $playerId) =>
            $playerData->ready === true && ($playerId === ($player?->getId() ?? $playerId))
        );

        return $readyPlayersData->count() === (isset($player) ? 1 : $this->players->count());
    }
}
