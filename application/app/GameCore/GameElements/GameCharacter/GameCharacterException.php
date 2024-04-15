<?php

namespace App\GameCore\GameElements\GameCharacter;

use Exception;

class GameCharacterException extends Exception
{
    public const MESSAGE_WRONG_NAME = 'Incorrect character name';
}
