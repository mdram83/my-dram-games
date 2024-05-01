<?php

namespace App\Games\Thousand;

use App\GameCore\GameElements\GameBoard\GameBoardException;
use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCardUnique;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealer;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealerException;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDeckProvider;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardRank;
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
use App\Games\Thousand\Elements\GamePhaseThousandPlayFirstCard;
use App\Games\Thousand\Elements\GamePhaseThousandPlaySecondCard;
use App\Games\Thousand\Elements\GamePhaseThousandPlayThirdCard;
use App\Games\Thousand\Elements\GamePhaseThousandRepository;
use Games\Thousand\Elements\GamePhaseThousandPlayThirdCardTest;

class GamePlayThousand extends GamePlayBase implements GamePlay
{
    protected PlayingCardDeckProvider $deckProvider;
    protected PlayingCardSuitRepository $suitRepository;
    protected PlayingCardDealer $cardDealer;
    protected GamePhaseThousandRepository $phaseRepository;

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

    private array $acesKeys = [['A-H'], ['A-D'], ['A-C'], ['A-S']];
    private array $marriagesKeys = [
        100 => ['K-H', 'Q-H'],
        80  => ['K-D', 'Q-D'],
        60  => ['K-C', 'Q-C'],
        40  => ['K-S', 'Q-S'],
    ];

    /**
     * @throws GamePlayException|GameMoveException|CollectionException
     * @throws GameResultProviderException
     * @throws GameResultException
     */
    public function handleMove(GameMove $move): void
    {
        if ($this->isFinished()) {
            throw new GamePlayException(GamePlayException::MESSAGE_MOVE_ON_FINISHED_GAME);
        }

        if (is_a($move, GameMoveThousandSorting::class)) {

            $this->handleMoveSorting($move);

        } elseif (is_a($move, GameMoveThousandCountPoints::class)) {

            $this->handleMoveCountPoints($move);

        } else {
            $this->validateMove($move);
            $this->handleMoveByPhase($move);
        }

        $this->saveData();

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
        $this->turnSuit = null;
        $this->turnLead = null;
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
            $this->stockRecord = $this->stockRecord->reset($this->stock->toArray());

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

    /**
     * @throws GamePlayThousandException
     */
    private function handleMoveDeclaration(GameMove $move): void
    {
        $declaration = $move->getDetails()['declaration'];
        $this->validateMoveDeclaration($declaration);

        if ($declaration === 0) {

            $barrelFrom = $this->getGameInvite()->getGameSetup()->getOption('thousand-barrel-points')->getConfiguredValue()->getValue();

            foreach ($this->players->toArray() as $player) {

                $this->countPlayerPoints($player, true);

                $this->playersData[$player->getId()]['ready'] = false;
                $this->playersData[$player->getId()]['barrel'] =
                    $barrelFrom > 0
                    && $this->playersData[$player->getId()]['points'][$this->round] >= $barrelFrom;
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

        if ($move->getDetails()['phase']->getKey() === GamePhaseThousandPlayFirstCard::PHASE_KEY) {
            $this->turnSuit = $this->table->getOne($cardKey)->getSuit();

            if ($hasMarriageRequest) {
                $this->trumpSuit = $this->turnSuit;
                $this->playersData[$this->activePlayer->getId()]['trumps'][] = $cardKey;
            }
        }

        if ($this->phase->getKey() === GamePhaseThousandPlayThirdCard::PHASE_KEY) {

            $tableWithPlayersKeys = [];
            $player = $this->turnLead;
            foreach($this->table->toArray() as $card) {
                $tableWithPlayersKeys[] = ['player' => $player, 'card' => $card];
                $player = $this->getNextOrderedPlayer($player);
            }

            $winningCard = $tableWithPlayersKeys[0]['card'];
            $winningIndex = 0;

            for ($i = 1; $i <= 2; $i++) {
                $nextCard = $tableWithPlayersKeys[$i]['card'];
                $isNextBetter = false;

                // next in trumpSuit beat previuos not in trumpSuit
                if (
                    $nextCard->getSuit()->getKey() === $this->trumpSuit?->getKey()
                    && $winningCard->getSuit()->getKey() !== $this->trumpSuit?->getKey()
                ) {
                    $isNextBetter = true;
                }

                // next in trumpSuit beat previous in trump BY RANK
                if (
                    $nextCard->getSuit()->getKey() === $this->trumpSuit?->getKey()
                    && $winningCard->getSuit()->getKey() === $this->trumpSuit?->getKey()
                    && $this->getCardPoints($nextCard) > $this->getCardPoints($winningCard)
                ) {
                    $isNextBetter = true;
                }

                // next not in trumpSuite but in turnSuite beat previous not in trumpSuit BY RANK
                if (
                    $nextCard->getSuit()->getKey() !== $this->trumpSuit?->getKey()
                    && $winningCard->getSuit()->getKey() !== $this->trumpSuit?->getKey()
                    && $nextCard->getSuit()->getKey() === $this->turnSuit->getKey()
                    && $this->getCardPoints($nextCard) > $this->getCardPoints($winningCard)
                ) {
                    $isNextBetter = true;
                }

                if ($isNextBetter) {
                    $winningCard = $nextCard;
                    $winningIndex = $i;
                }
            }

            $winningPlayer = $tableWithPlayersKeys[$winningIndex]['player'];
            $this->cardDealer->moveCardsTimes($this->table, $this->playersData[$winningPlayer->getId()]['tricks'], 3);
            $this->activePlayer = $winningPlayer;
            $this->turnLead = $winningPlayer;
            $this->turnSuit = null;

            if ($this->playersData[$move->getPlayer()->getId()]['hand']->count() === 0) {

                $barrelFrom = $this->getGameInvite()->getGameSetup()->getOption('thousand-barrel-points')->getConfiguredValue()->getValue();

                foreach ($this->players->toArray() as $player) {

                    $this->countPlayerPoints($player, false);

                    $this->playersData[$player->getId()]['tricks'] = $this->cardDealer->getEmptyStock();
                    $this->playersData[$player->getId()]['trumps'] = [];
                    $this->playersData[$player->getId()]['ready'] = false;
                    $this->playersData[$player->getId()]['barrel'] =
                        $barrelFrom > 0
                        && $this->playersData[$player->getId()]['points'][$this->round] >= $barrelFrom;
                }

                $this->trumpSuit = null;
                $this->turnLead = null;
                $this->stockRecord = $this->cardDealer->getEmptyStock();
                $this->activePlayer = $this->bidWinner;
            }

        } else {
            $this->activePlayer = $this->getNextOrderedPlayer($move->getPlayer());
        }

        $isLastAttempt = $this->phase->getKey() !== GamePhaseThousandPlayThirdCard::PHASE_KEY || $hand->count() === 0;
        $this->phase = $this->phase->getNextPhase($isLastAttempt);
    }

    /**
     * @throws GamePlayException
     */
    private function handleMoveCountPoints(GameMove $move): void
    {
        if ($this->playersData[$move->getPlayer()->getId()]['ready']) {
            throw new GamePlayException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
        }

        $this->playersData[$move->getPlayer()->getId()]['ready'] = true;

        if (count(array_filter(array_map(fn($item) => $item['ready'], $this->playersData), fn($item) => !$item)) === 0) {

            $this->dealer = $this->getNextOrderedPlayer($this->dealer);
            $this->obligation = $this->getNextOrderedPlayer($this->dealer);
            $this->activePlayer = $this->getNextOrderedPlayer($this->obligation);

            $this->bidWinner = null;
            $this->bidAmount = 100;
            $this->playersData[$this->obligation->getId()]['bid'] = $this->bidAmount;

            $this->shuffleAndDealCards();

            $this->round++;
            $this->phase = $this->phase->getNextPhase(true);
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

            $allowedBombMoves = $this
                ->getGameInvite()
                ->getGameSetup()
                ->getOption('thousand-number-of-bombs')
                ->getConfiguredValue()
                ->getValue();

            if (count($this->playersData[$this->getActivePlayer()->getId()]['bombRounds']) >= $allowedBombMoves) {
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

            if ($this->phase->getKey() !== GamePhaseThousandPlayFirstCard::PHASE_KEY) {
                throw new GamePlayException(GamePlayException::MESSAGE_INCOMPATIBLE_MOVE);
            }
        }

        if ($this->phase->getKey() !== GamePhaseThousandPlayFirstCard::PHASE_KEY) {
            if (
                $hand->filter(fn($item, $key) => $item->getSuit() === $this->turnSuit)->count() > 0
                && $hand->getOne($cardKey)->getSuit() !== $this->turnSuit
            ) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_PLAY_TURN_SUIT);
            }
        }

        if ($this->phase->getKey() === GamePhaseThousandPlaySecondCard::PHASE_KEY) {

            $tableCard = $this->table->getOne($this->cardDealer->getCardsKeys($this->table)[0]);
            $mustPlayRankCards = $hand->filter(fn($item, $key) => (
                $this->getCardPoints($item) > $this->getCardPoints($tableCard)
                && $item->getSuit() === $this->turnSuit
            ));

            if ($mustPlayRankCards->count() > 0 && !$mustPlayRankCards->exist($cardKey)) {
                throw new GamePlayThousandException(GamePlayThousandException::MESSAGE_RULE_PLAY_HIGH_RANK);
            }
        }
    }

    private function countPlayerPoints(Player $player, bool $isBombMove = false): void
    {
            $playerId = $player->getId();
            $isFourPlayersDealer = ($this->players->count() === 4 && $playerId === $this->dealer->getId());
            $isOnBarrel = $this->playersData[$playerId]['barrel'];

            $pointsPreviousRounds = $this->playersData[$playerId]['points'][$this->round - 1] ?? 0;

            $pointsCurrentRound =
                $isBombMove
                    ? ((
                        $playerId === $this->activePlayer->getId()
                        || $isFourPlayersDealer
                        || $isOnBarrel // ADDED
                    ) ? 0 : 60)
                    : $this->collectPointsFromTricksAndTrumps($player);

            $stockPoints =
                (!$isFourPlayersDealer || $isOnBarrel)
                    ? 0
                    : (
                        $this->stockRecord->exist($this->acesKeys[0]) * 50
                        + $this->stockRecord->exist($this->acesKeys[1]) * 50
                        + $this->stockRecord->exist($this->acesKeys[2]) * 50
                        + $this->stockRecord->exist($this->acesKeys[3]) * 50
                        + ($this->cardDealer->hasStockAnyCombination($this->stockRecord, [$this->marriagesKeys[100]]) ? 100 : 0)
                        + ($this->cardDealer->hasStockAnyCombination($this->stockRecord, [$this->marriagesKeys[80]]) ? 80 : 0)
                        + ($this->cardDealer->hasStockAnyCombination($this->stockRecord, [$this->marriagesKeys[60]]) ? 60 : 0)
                        + ($this->cardDealer->hasStockAnyCombination($this->stockRecord, [$this->marriagesKeys[40]]) ? 40 : 0)
                    );

            $this->playersData[$playerId]['points'][$this->round] =
                $pointsPreviousRounds
                + (
                    ($this->playersData[$playerId]['barrel'] && $player->getId() !== $this->bidWinner->getId())
                        ? 0
                        : ($pointsCurrentRound + $stockPoints)
                );
    }

    private function collectPointsFromTricksAndTrumps(Player $player): int
    {
        if ($player->getId() !== $this->bidWinner->getId() && $this->playersData[$player->getId()]['barrel']) {
            return 0;
        }

        $trickPoints = 0;
        foreach ($this->playersData[$player->getId()]['tricks']->toArray() as $card) {
            $trickPoints += $this->getCardPoints($card);
        }


        $trumpPoints = 0;
        foreach ($this->playersData[$player->getId()]['trumps'] as $trumpCardKey) {
            foreach ($this->marriagesKeys as $points => $marriageKeys) {
                $trumpPoints += in_array($trumpCardKey, $marriageKeys) ? $points : 0;
            }
        }


        $totalPoints = $trickPoints + $trumpPoints;

        if ($player->getId() === $this->bidWinner->getId()) {
            $totalPoints = $this->bidAmount * ($totalPoints < $this->bidAmount ? -1 : 1);
        }

        return (int)round($totalPoints, -1);
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

    private function getCardPoints(PlayingCard $card): int
    {
        return match($card->getRank()->getKey()) {
            'A' => 11,
            '10' => 10,
            'K' => 4,
            'Q' => 3,
            'J' => 2,
            '9' => 0,
        };
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
}
