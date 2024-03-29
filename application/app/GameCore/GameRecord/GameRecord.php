<?php

namespace App\GameCore\GameRecord;

use App\GameCore\GameInvite\GameInvite;
use App\GameCore\GamePlay\GamePlay;
use App\GameCore\Player\Player;

interface GameRecord
{
    public function getId(): int|string;
    public function getPlayer(): Player;
    public function getSlug(): string;
    public function getScore(): string;
    public function isWinner(): bool;
    public function getGameInvite(): GameInvite;
    public function getGamePlay(): GamePlay;
}
