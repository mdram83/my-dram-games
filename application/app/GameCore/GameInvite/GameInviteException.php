<?php

namespace App\GameCore\GameInvite;

use Exception;

class GameInviteException extends Exception
{
    public const MESSAGE_GAME_NOT_FOUND = 'GameInvite not found';

    public const MESSAGE_PLAYER_ALREADY_ADDED = 'Player already added';
    public const MESSAGE_TOO_MANY_PLAYERS = 'Number of players exceeded';
    public const MESSAGE_NO_OF_PLAYERS_EXCEED_DEF = 'Number of players not matching game definition';
    public const MESSAGE_NO_OF_PLAYERS_NOT_SET = 'Number of players not set';
    public const MESSAGE_NO_OF_PLAYERS_WAS_SET = 'Number of players already set';
    public const MESSAGE_PLAYER_TYPE_UNSET = 'Player type unknown';

    public const MESSAGE_HOST_ALREADY_ADDED = 'Host already added';
    public const MESSAGE_HOST_NOT_SET = 'Host not set';

    public const MESSAGE_GAME_DEFINITION_NOT_SET = 'GameInvite definition not set';
    public const MESSAGE_GAME_DEFINITION_WAS_SET = 'GameInvite definition already set';
}
