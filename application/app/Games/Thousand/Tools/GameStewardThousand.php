<?php

namespace App\Games\Thousand\Tools;

use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCardUnique;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealer;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardSuit;
use App\GameCore\GameElements\GamePhase\GamePhase;
use App\GameCore\GameElements\GamePlayPlayers\CollectionGamePlayPlayers;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\Player\Player;
use App\Games\Thousand\Elements\GamePhaseThousandPlayFirstCard;
use App\Games\Thousand\Elements\GamePhaseThousandPlaySecondCard;
use App\Games\Thousand\Elements\GamePhaseThousandPlayThirdCard;

class GameStewardThousand
{
    private array $acesKeys = [['A-H'], ['A-D'], ['A-C'], ['A-S']];
    private array $marriagesKeys = [
        100 => ['K-H', 'Q-H'],
        80  => ['K-D', 'Q-D'],
        60  => ['K-C', 'Q-C'],
        40  => ['K-S', 'Q-S'],
    ];

    public function __construct(
        readonly private CollectionGamePlayPlayers $players,
        readonly private GameInvite $invite,
        readonly private PlayingCardDealer $dealer,
    )
    {

    }

    public function isFirstCardPhase(GamePhase $phase): bool
    {
        return $phase->getKey() === GamePhaseThousandPlayFirstCard::PHASE_KEY;
    }

    public function isSecondCardPhase(GamePhase $phase): bool
    {
        return $phase->getKey() === GamePhaseThousandPlaySecondCard::PHASE_KEY;
    }

    public function isThirdCardPhase(GamePhase $phase): bool
    {
        return $phase->getKey() === GamePhaseThousandPlayThirdCard::PHASE_KEY;
    }

    public function getNextOrderedPlayer(Player $player, Player $dealer, array $playersData): Player
    {
        $playerSeat = $playersData[$player->getId()]['seat'];
        $nextPlayerSeat = ($playerSeat % $this->players->count()) + 1;

        $nextPlayerId = array_keys(array_filter(
            $playersData,
            fn($playerData) => $playerData['seat'] === $nextPlayerSeat
        ))[0];

        $nextPlayer = $this->players->getOne($nextPlayerId);

        return
            (
                $playersData[$nextPlayerId]['bid'] === 'pass'
                || $this->isFourPlayersDealer($nextPlayer, $dealer)
            )
                ? $this->getNextOrderedPlayer($nextPlayer, $dealer, $playersData)
                : $nextPlayer;
    }

    public function shuffleAndDealCards(
        CollectionPlayingCardUnique $deck,
        CollectionPlayingCardUnique $stock,
        Player $dealer,
        array $playersData
    ): void
    {
        $definition = [['stock' => $stock, 'numberOfCards' => 3]];
        $nextPlayer = $dealer;

        for ($i = 1; $i <= 3; $i++) {
            $nextPlayer = $this->getNextOrderedPlayer($nextPlayer, $dealer, $playersData);
            $definition[] = ['stock' => $playersData[$nextPlayer->getId()]['hand'], 'numberOfCards' => 7];
        }

        $this->dealer->shuffleAndDealCards($deck, $definition);
    }

    public function hasMarriageAtHand(Player $player, array $playersData): bool
    {
        return $this->dealer->hasStockAnyCombination(
            $playersData[$player->getId()]['hand'],
            $this->marriagesKeys
        );
    }

    public function isLastBiddingMove(int $bidAmount, array $playersData): bool
    {
        return
            $bidAmount === 300
            || count(array_filter($playersData, fn($playerData) => $playerData['bid'] === 'pass')) === 2;
    }

    public function getHighestBiddingPlayer(array $playersData): Player
    {
        $bids = array_filter(
            array_map(fn($playerData) => $playerData['bid'], $playersData),
            fn($bid) => ($bid !== null && $bid !== 'pass')
        );

        return $this->players->getOne(array_search(max($bids), $bids));
    }

    public function isFourPlayersDealer(Player $player, Player $dealer): bool
    {
        return ($this->players->count() === 4 && $player->getId() === $dealer->getId());
    }

    public function hasPlayerUsedMaxBombMoves(Player $player, array $playersData): bool
    {
        $allowedBombMoves = $this->invite
            ->getGameSetup()
            ->getOption('thousand-number-of-bombs')
            ->getConfiguredValue()
            ->getValue();

        return count($playersData[$player->getId()]['bombRounds']) >= $allowedBombMoves;
    }

    public function getCardPoints(PlayingCard $card): int
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

    public function getTrickWinner(
        Player $dealer,
        Player $turnLead,
        ?PlayingCardSuit $trumpSuit,
        ?PlayingCardSuit $turnSuit,
        CollectionPlayingCardUnique $table,
        array $playersData,
    ): Player
    {
        $tableWithPlayers = [];
        $player = $turnLead;
        $trumpSuitKey = $trumpSuit?->getKey();
        $turnSuitKey = $turnSuit?->getKey();

        foreach ($table->toArray() as $card) {
            $tableWithPlayers[] = ['player' => $player, 'card' => $card];
            $player = $this->getNextOrderedPlayer($player, $dealer, $playersData);
        }

        $winningCard = $tableWithPlayers[0]['card'];
        $winningIndex = 0;

        for ($i = 1; $i <= 2; $i++) {

            $nextCard = $tableWithPlayers[$i]['card'];
            $isNextBetter = false;

            $nextCardSuite = $nextCard->getSuit()->getKey();
            $winningCardSuit = $winningCard->getSuit()->getKey();

            // next in trumpSuit beat previous not in trumpSuit
            if ($nextCardSuite === $trumpSuitKey && $winningCardSuit !== $trumpSuitKey) {
                $isNextBetter = true;
            }

            // next in trumpSuit beat previous in trumpSuit by Rank
            if (
                $nextCardSuite === $trumpSuitKey
                && $winningCardSuit === $trumpSuitKey
                && $this->getCardPoints($nextCard) > $this->getCardPoints($winningCard)
            ) {
                $isNextBetter = true;
            }

            // next not in trumpSuite but in turnSuite beat previous not in trumpSuit BY RANK
            if (
                $nextCardSuite !== $trumpSuitKey
                && $winningCardSuit !== $trumpSuitKey
                && $nextCardSuite === $turnSuitKey
                && $this->getCardPoints($nextCard) > $this->getCardPoints($winningCard)
            ) {
                $isNextBetter = true;
            }

            if ($isNextBetter) {
                $winningCard = $nextCard;
                $winningIndex = $i;
            }
        }

        return $tableWithPlayers[$winningIndex]['player'];
    }

    public function setRoundPoints(
        Player $player,
        Player $dealer,
        Player $activePlayer,
        Player $bidWinner,
        int $bidAmount,
        int $round,
        CollectionPlayingCardUnique $stockRecord,
        array &$playersData,
        bool $isBombMove = false,
    ): void
    {
        $playerId = $player->getId();
        $isFourPlayersDealer = $this->isFourPlayersDealer($player, $dealer);
        $isOnBarrel = $playersData[$playerId]['barrel'];

        $pointsCurrentRound =  $isBombMove
            ? (($playerId === $activePlayer->getId() || $isFourPlayersDealer || $isOnBarrel) ? 0 : 60)
            : $this->countPlayedPoints($player, $bidWinner, $bidAmount, $playersData);

        $stockPoints = ($isFourPlayersDealer && !$isOnBarrel)
            ? (
                $this->countStockAcesPoints($stockRecord)
                + $this->countStockMarriagePoints($stockRecord)
            )
            : 0;

        $playersData[$playerId]['points'][$round] =
            ($playersData[$playerId]['points'][$round - 1] ?? 0)
            + (
            !$this->isPlayerEligibleForPoints($player, $bidWinner, $playersData)
                ? 0
                : ($pointsCurrentRound + $stockPoints)
            );
    }

    public function isPlayerEligibleForPoints(Player $player, Player $bidWinner, array $playersData): bool
    {
        return !$playersData[$player->getId()]['barrel'] || $player->getId() === $bidWinner->getId();
    }

    public function countStockMarriagePoints(CollectionPlayingCardUnique $stock): int
    {
        $cumulatedPoints = 0;

        foreach ($this->marriagesKeys as $points => $pair) {
            $cumulatedPoints += $this->dealer->hasStockAnyCombination(
                $stock, [$this->marriagesKeys[$points]]
            ) ? $points : 0;
        }

        return $cumulatedPoints;
    }

    public function countStockAcesPoints(CollectionPlayingCardUnique $stock): int
    {
        return $this->dealer->countStockMatchingCombinations($stock, $this->acesKeys) * 50;
    }

    public function countPlayedPoints(Player $player, Player $bidWinner, int $bidAmount, array $playersData): int
    {
        $points = $this->countTricksPoints($player, $playersData) + $this->countTrumpsPoints($player, $playersData);

        if ($player->getId() === $bidWinner->getId()) {
            $points = $bidAmount * ($points < $bidAmount ? -1 : 1);
        }

        return (int)round($points, -1);
    }

    private function countTricksPoints(Player $player, array $playersData): int
    {
        $cards = $playersData[$player->getId()]['tricks']->toArray();
        return array_reduce($cards, fn($points, $card) => $points + $this->getCardPoints($card), 0);
    }

    private function countTrumpsPoints(Player $player, array $playersData): int
    {
        $trumpDeclarationKeys = $playersData[$player->getId()]['trumps'];

        return array_reduce($trumpDeclarationKeys, function($points, $cardKey) {
            foreach ($this->marriagesKeys as $trumpPoints => $marriageKeys) {
                $points += in_array($cardKey, $marriageKeys) ? $trumpPoints : 0;
            }
            return $points;
        }, 0);
    }

    public function setBarrelStatus(Player $player, int $round, array &$playersData): void
    {
        $pointsLimit = $this->invite
            ->getGameSetup()
            ->getOption('thousand-barrel-points')
            ->getConfiguredValue()
            ->getValue();

        $playersData[$player->getId()]['barrel'] =
            $pointsLimit > 0
            && $playersData[$player->getId()]['points'][$round] >= $pointsLimit;
    }
}
