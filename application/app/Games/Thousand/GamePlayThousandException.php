<?php

namespace App\Games\Thousand;

use App\GameCore\GamePlay\GamePlayException;

class GamePlayThousandException extends GamePlayException
{
    public const MESSAGE_RULE_BID_STEP_INVALID = 'Bid amount do not follow expected sequence';
    public const MESSAGE_RULE_BID_NO_MARRIAGE = 'Can not bid over 120 points without marriage at hand';
    public const MESSAGE_RULE_WRONG_DECLARATION = 'Invalid declaration value';
}
