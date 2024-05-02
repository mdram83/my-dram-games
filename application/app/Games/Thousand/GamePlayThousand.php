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
use App\GameCore\Player\Player;
use App\GameCore\Services\Collection\CollectionException;
use App\Games\Thousand\Elements\GameMoveThousand;
use App\Games\Thousand\Elements\GameMoveThousandBidding;
use App\Games\Thousand\Elements\GameMoveThousandCountPoints;
use App\Games\Thousand\Elements\GameMoveThousandDeclaration;
use App\Games\Thousand\Elements\GameMoveThousandPlayCard;
use App\Games\Thousand\Elements\GameMoveThousandSorting;
use App\Games\Thousand\Elements\GameMoveThousandStockDistribution;
use App\Games\Thousand\Elements\GamePhaseThousandBidding;
use App\Games\Thousand\Elements\GamePhaseThousandCountPoints;
use App\Games\Thousand\Elements\GamePhaseThousandRepository;
use App\Games\Thousand\Tools\GameStewardThousand;

class GamePlayThousand extends GamePlayBase implements GamePlay
{
    protected PlayingCardDeckProvider $deckProvider;
    protected PlayingCardSuitRepository $suitRepository;
    protected PlayingCardDealer $cardDealer;
    protected GamePhaseThousandRepository $phaseRepository;
    protected GameStewardThousand $steward;

    protected ?GameResultThousand $result = null;

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
    private ?PlayingCardSuit $turnSuit;
    private ?Player $turnLead;
    private GamePhase $phase;

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
        if ($this->round > 1) {

            $resultProvider = new GameResultProviderThousand(clone $this->collectionHandler, $this->gameRecordFactory);

            if ($this->result = $resultProvider->getResult([
                'players' => $this->players,
                'playersData' => $this->playersData,
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
            'playersData' => $this->playersData,
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

        $situationData = $this->getSituationData();

        foreach ($situationData['orderedPlayers'] as $playerName => $playerData) {

            if ($playerName !== $player->getName()) {
                $situationData['orderedPlayers'][$playerName]['hand'] = count($situationData['orderedPlayers'][$playerName]['hand']);
            }

            $situationData['orderedPlayers'][$playerName]['tricks'] = count($situationData['orderedPlayers'][$playerName]['tricks']);
        }

        $situationData['stock'] = count($situationData['stock']);
        $situationData['stockRecord'] = (
            $this->bidAmount > 100
            || ($this->phase->getKey() === (new GamePhaseThousandCountPoints())->getKey()) && $this->players->count() === 4)
                ? $situationData['stockRecord']
                : [];

        if (isset($this->result)) {
            $situationData['result'] = $this->result->toArray();
        }

        return $situationData;
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
                'bombRounds' => $playerData['bombRounds'],
                'trumps' => $playerData['trumps'],
            ];
        }

        return [
            'orderedPlayers' => $orderedPlayers,

            'stock' => $this->cardDealer->getCardsKeys($this->stock),
            'stockRecord' => $this->cardDealer->getCardsKeys($this->stockRecord),
            'table' => $this->cardDealer->getCardsKeys($this->table),
            'trumpSuit' => $this->trumpSuit?->getKey(),
            'turnSuit' => $this->turnSuit?->getKey(),
            'turnLead' => $this->turnLead?->getName(),
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

    protected function initialize(): void
    {
        $this->initializePlayersData();
        $this->dealer = $this->players->getOne(array_keys($this->playersData)[0]);
        $this->setRoundStartParameters();

        $this->stock = $this->cardDealer->getEmptyStock();
        $this->stockRecord = $this->cardDealer->getEmptyStock();
        $this->table = $this->cardDealer->getEmptyStock();
        $this->deck = $this->deckProvider->getDeckSchnapsen();

        $this->steward->shuffleAndDealCards($this->deck, $this->stock, $this->dealer, $this->playersData);

        $this->round = 1;
        $this->trumpSuit = null;
        $this->turnSuit = null;
        $this->turnLead = null;
        $this->phase = new GamePhaseThousandBidding();

        $this->saveData();
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
                'bombRounds' => [],
                'trumps' => [],
            ];
            $seat++;
        }
    }

    private function setRoundStartParameters(): void
    {
        $this->obligation = $this->steward->getNextOrderedPlayer($this->dealer, $this->dealer, $this->playersData);
        $this->activePlayer = $this->steward->getNextOrderedPlayer($this->obligation, $this->dealer, $this->playersData);

        $this->bidWinner = null;
        $this->bidAmount = 100;
        $this->playersData[$this->obligation->getId()]['bid'] = $this->bidAmount;
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
                'bombRounds' => $playerData['bombRounds'],
                'trumps' => $playerData['trumps'],
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
        $this->turnSuit = isset($data['turnSuit']) ? $this->suitRepository->getOne($data['turnSuit']) : null;
        $this->turnLead = $this->getPlayerByName($data['turnLead']);
        $this->phase = $this->phaseRepository->getOne($data['phase']['key']);
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

        if ($this->phase->getKey() !== $move->getDetails()['phase']->getKey()) {
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
     * @throws GamePlayThousandException|CollectionException
     */
    private function handleMoveBidding(GameMove $move): void
    {
        if ($move->getDetails()['decision'] === 'bid') {

            $newBidAmount = $move->getDetails()['bidAmount'];

            if ($newBidAmount !== ($this->bidAmount + 10)) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_BID_STEP_INVALID);
            }

            if ($newBidAmount > 120 && !$this->steward->hasMarriageAtHand($move->getPlayer(), $this->playersData)) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_BID_NO_MARRIAGE);
            }

            $this->bidAmount = $newBidAmount;
        }

        $this->playersData[$move->getPlayer()->getId()]['bid'] = $move->getDetails()['bidAmount'] ?? 'pass';

        if ($this->steward->isLastBiddingMove($this->bidAmount, $this->playersData)) {

            $this->phase = $this->phase->getNextPhase(true);
            $this->activePlayer = $this->steward->getHighestBiddingPlayer($this->playersData);
            $this->bidWinner = $this->steward->getHighestBiddingPlayer($this->playersData);
            $this->stockRecord = $this->stockRecord->reset($this->stock->toArray());

            $this->cardDealer->collectCards($this->playersData[$this->bidWinner->getId()]['hand'], [$this->stock]);

            foreach ($this->playersData as $playerId => $playerData) {
                $this->playersData[$playerId]['bid'] = null;
            }

        } else {
            $this->phase = $this->phase->getNextPhase(false);
            $this->activePlayer = $this->steward->getNextOrderedPlayer($move->getPlayer(), $this->dealer, $this->playersData);
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

    /**
     * @throws GamePlayThousandException
     */
    private function handleMoveDeclaration(GameMove $move): void
    {
        $declaration = $move->getDetails()['declaration'];
        $this->validateMoveDeclaration($declaration);

        if ($declaration === 0) {

            foreach ($this->players->toArray() as $player) {
                $this->steward->setRoundPoints(
                    $player,
                    $this->dealer,
                    $this->activePlayer,
                    $this->bidWinner,
                    $this->bidAmount,
                    $this->round,
                    $this->stockRecord,
                    $this->playersData,
                    true
                );
                $this->steward->setBarrelStatus($player, $this->round, $this->playersData);
                $this->playersData[$player->getId()]['ready'] = false;
            }

            $this->playersData[$this->activePlayer->getId()]['bombRounds'][] = $this->round;
            $this->phase = new GamePhaseThousandCountPoints();

            return;
        }

        $this->bidAmount = $declaration;
        $this->turnLead = $this->bidWinner;
        $this->phase = $this->phase->getNextPhase(true);
    }

    /**
     * @throws GamePlayException
     */
    private function handleMovePlayCard(GameMove $move): void
    {
        $cardKey = $move->getDetails()['card'];
        $hand = $this->playersData[$move->getPlayer()->getId()]['hand'];
        $hasMarriageRequest = $move->getDetails()['marriage'] ?? false;

        $this->validateMovePlayingCard($hand, $cardKey, $hasMarriageRequest);
        $this->cardDealer->moveCardsByKeys($hand, $this->table, [$cardKey]);

        if ($this->steward->isFirstCardPhase($this->phase)) {

            $this->turnSuit = $this->table->getOne($cardKey)->getSuit();

            if ($hasMarriageRequest) {
                $this->trumpSuit = $this->turnSuit;
                $this->playersData[$this->activePlayer->getId()]['trumps'][] = $cardKey;
            }
        }

        if ($this->steward->isThirdCardPhase($this->phase)) {

            $trickWinner = $this->steward->getTrickWinner(
                $this->dealer,
                $this->turnLead,
                $this->trumpSuit,
                $this->turnSuit,
                $this->table,
                $this->playersData
            );

            $this->cardDealer->moveCardsTimes(
                $this->table, $this->playersData[$trickWinner->getId()]['tricks'],
                3
            );

            $this->activePlayer = $trickWinner;
            $this->turnLead = $trickWinner;
            $this->turnSuit = null;

            if ($hand->count() === 0) {

                foreach ($this->players->toArray() as $player) {

                    $this->steward->setRoundPoints(
                        $player,
                        $this->dealer,
                        $this->activePlayer,
                        $this->bidWinner,
                        $this->bidAmount,
                        $this->round,
                        $this->stockRecord,
                        $this->playersData,
                        false
                    );
                    $this->steward->setBarrelStatus($player, $this->round, $this->playersData);

                    $this->playersData[$player->getId()]['tricks'] = $this->cardDealer->getEmptyStock();
                    $this->playersData[$player->getId()]['trumps'] = [];
                    $this->playersData[$player->getId()]['ready'] = false;
                }

                $this->trumpSuit = null;
                $this->turnLead = null;
                $this->stockRecord = $this->cardDealer->getEmptyStock();
                $this->activePlayer = $this->bidWinner;
            }

        } else {
            $this->activePlayer = $this->steward->getNextOrderedPlayer($move->getPlayer(), $this->dealer, $this->playersData);
        }

        $isLastPhaseAttempt = !$this->steward->isThirdCardPhase($this->phase) || $hand->count() === 0;
        $this->phase = $this->phase->getNextPhase($isLastPhaseAttempt);
    }

    /**
     * @throws GamePlayException
     */
    private function handleMoveCountPoints(GameMove $move): void
    {
        if ($this->arePlayersReady($move->getPlayer())) {
            throw new GamePlayException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }

        $this->playersData[$move->getPlayer()->getId()]['ready'] = true;

        if ($this->arePlayersReady()) {

            $this->dealer = $this->steward->getNextOrderedPlayer($this->dealer, $this->dealer, $this->playersData);
            $this->setRoundStartParameters();

            $this->steward->shuffleAndDealCards($this->deck, $this->stock, $this->dealer, $this->playersData);

            $this->round++;
            $this->phase = $this->phase->getNextPhase(true);
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
            || ($numberOfPlayers === 4 && in_array($this->dealer->getName(), array_keys($distribution)))
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

            if ($this->bidAmount !== 100) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_BOMB_ON_BID);
            }

            if ($this->steward->hasPlayerUsedMaxBombMoves($this->activePlayer, $this->playersData)) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_BOMB_USED);
            }

        } elseif (($declaration < $this->bidAmount || $declaration > 300 || $declaration % 10 !== 0)) {
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

            if (!$this->steward->isFirstCardPhase($this->phase)) {
                throw new GamePlayException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
            }
        }

        if (!$this->steward->isFirstCardPhase($this->phase)) {
            if (
                $hand->filter(fn($item, $key) => $item->getSuit() === $this->turnSuit)->count() > 0
                && $hand->getOne($cardKey)->getSuit() !== $this->turnSuit
            ) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_PLAY_TURN_SUIT);
            }
        }

        if ($this->steward->isSecondCardPhase($this->phase)) {

            $tableCard = $this->table->getOne($this->cardDealer->getCardsKeys($this->table)[0]);
            $higherRankCardsAtHand = $hand->filter(fn($item, $key) => (
                $this->steward->getCardPoints($item) > $this->steward->getCardPoints($tableCard)
                && $item->getSuit() === $this->turnSuit
            ));

            if ($higherRankCardsAtHand->count() > 0 && !$higherRankCardsAtHand->exist($cardKey)) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_PLAY_HIGH_RANK);
            }
        }
    }

    private function arePlayersReady(?Player $player = null): bool
    {
        $readyPlayersData = array_filter(
            $this->playersData,
            fn($playerData, $playerId) => ($playerId === ($player?->getId() ?? $playerId)) && $playerData['ready'],
            ARRAY_FILTER_USE_BOTH
        );

        return count($readyPlayersData) === (isset($player) ? 1 : $this->players->count());
    }

}
