<?php

namespace App\Models\GameCore\GameDefinition;

class GameDefinitionException extends \Exception
{
    public const MESSAGE_GAME_DEFINITION_MISSING = 'Missing game configuration';
    public const MESSAGE_INCORRECT_CONFIGURATION = 'Incorrect game configuration';
}
