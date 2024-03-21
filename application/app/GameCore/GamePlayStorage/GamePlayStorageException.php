<?php

namespace App\GameCore\GamePlayStorage;

class GamePlayStorageException extends \Exception
{
    public const MESSAGE_NOT_FOUND = 'GamePlay not found';
    public const MESSAGE_INVALID_INVITE = 'GameInvite invalid';
    public const MESSAGE_INVITE_NOT_SET = 'GameInvite not set';
    public const MESSAGE_SETUP_ALREADY_SET = 'Setup already set';
}
