<?php

namespace App\GameCore\GameRecord;

use Exception;

class GameRecordException extends Exception
{
    public const MESSAGE_DUPLICATE_RECORD = 'Game record duplicated';
    public const MESSAGE_MISSING_INVITE = 'Incorrect game invite';
}
