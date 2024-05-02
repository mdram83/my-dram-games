<?php

namespace App\Games\Thousand\Tools;

use App\GameCore\GameElements\GameDeck\PlayingCard\CollectionPlayingCardUnique;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCard;
use App\GameCore\GameElements\GameDeck\PlayingCard\PlayingCardDealer;
use App\GameCore\GameElements\GamePhase\GamePhase;
use App\GameCore\GameElements\GamePlayPlayers\CollectionGamePlayPlayers;
use App\GameCore\GameInvite\GameInvite;
use App\GameCore\Player\Player;
use App\Games\Thousand\Elements\GamePhaseThousandPlayFirstCard;
use App\Games\Thousand\Elements\GamePhaseThousandPlaySecondCard;
use App\Games\Thousand\Elements\GamePhaseThousandPlayThirdCard;

class GameStewardThousand
{
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

    // TODO add method countStockAcesPoints()... to be used in Thousand setRoundPOints instead of directly calling dealer and acesKeys parameter

}
