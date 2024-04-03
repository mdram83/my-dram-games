<?php

namespace App\GameCore\GamePlayDisconnection;

class GamePlayDisconnectException extends \Exception
{
    public const MESSAGE_GAMEPLAY_ALREADY_SET = 'GamePlay already set';
    public const MESSAGE_PLAYER_ALREADY_SET = 'Player already set';
    public const MESSAGE_TIMESTAMP_NOT_SET = 'Timestamp not set';
    public const MESSAGE_DELETING_BEFORE_SAVE = 'Can not remove unsaved disconnection';
    public const MESSAGE_RECORD_ALREADY_EXIST = 'Disconnection already exist';
}
