<?php

namespace App\Services\GamePlayDisconnection;

use Exception;

class GamePlayDisconnectException extends Exception
{
    public const string MESSAGE_GAMEPLAY_ALREADY_SET = 'GamePlay already set';
    public const string MESSAGE_PLAYER_ALREADY_SET = 'Player already set';
    public const string MESSAGE_TIMESTAMP_NOT_SET = 'Timestamp not set';
    public const string MESSAGE_DELETING_BEFORE_SAVE = 'Can not remove unsaved disconnection';
    public const string MESSAGE_RECORD_ALREADY_EXIST = 'Disconnection already exist';
}
