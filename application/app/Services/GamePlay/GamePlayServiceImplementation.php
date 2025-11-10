<?php

namespace App\Services\GamePlay;

use App\Events\GamePlay\GamePlayMovedEvent;
use App\Services\GamePlay\GamePlayService;
use MyDramGames\Core\Exceptions\GameBoxException;
use MyDramGames\Core\GameMove\GameMove;
use MyDramGames\Core\GameMove\GameMoveFactory;
use MyDramGames\Core\GamePlay\GamePlay;
use MyDramGames\Utils\Player\Player;

class GamePlayServiceImplementation implements GamePlayService
{

    /**
     * @inheritDoc
     */
    public function getGameMove(Player $player, GamePlay $gamePlay, array $moveInputs): GameMove
    {
        /** @var GameMoveFactory $className */
        $className = $gamePlay->getGameInvite()->getGameBox()->getGameMoveFactoryClassname();
        return $className::create($player, $moveInputs);
    }

    public function dispatchGamePlayMovedEventToAllPlayers(GamePlay $gamePlay): void
    {
        foreach ($gamePlay->getGameInvite()->getPlayers()->toArray() as $player) {
            GamePlayMovedEvent::dispatch($gamePlay, $player);
        }
    }

    public function getShowResponseContent(Player $player, GamePlay $gamePlay): array
    {
        $options = array_map(
            fn($item) => $item->getConfiguredValue(),
            $gamePlay->getGameInvite()->getGameSetup()->getAllOptions()->toArray()
        );

        return [
            'gamePlayId' => $gamePlay->getId(),
            'gameInvite' => [
                'gameInviteId' => $gamePlay->getGameInvite()->getId(),
                'slug' => $gamePlay->getGameInvite()->getGameBox()->getSlug(),
                'name' => $gamePlay->getGameInvite()->getGameBox()->getName(),
                'host' => $gamePlay->getGameInvite()->getHost()->getName(),
                'options' => $options,
            ],
            'situation' => $gamePlay->getSituation($player)
        ];
    }
}
