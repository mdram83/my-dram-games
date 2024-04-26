<?php

namespace App\Games\Thousand;

use App\GameCore\GamePlay\GamePlayException;

class GamePlayThousandException extends GamePlayException
{
    public const MESSAGE_RULE_BID_STEP_INVALID = 'Bid amount do not follow expected sequence';
    public const MESSAGE_RULE_BID_NO_MARRIAGE = 'Can not bid over 120 points without marriage at hand';
    public const MESSAGE_RULE_WRONG_DECLARATION = 'Invalid declaration value';
    public const MESSAGE_RULE_BOMB_ON_BID = 'Can not use bomb after bidding over 100 points';
    public const MESSAGE_RULE_BOMB_USED = 'Can not use more bomb moves';
    public const MESSAGE_RULE_PLAY_TURN_SUIT = 'Need to follow led Suit if available at hand';
    public const MESSAGE_RULE_PLAY_HIGH_RANK = 'Need to play higher rank card if available at hand';
}
