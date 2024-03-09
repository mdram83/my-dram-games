<?php

namespace App\GameCore\GameOptionValue;

enum GameOptionValueNumberOfPlayers: int implements GameOptionValue
{
    case Players002 = 2;
    case Players003 = 3;
    case Players004 = 4;
    case Players005 = 5;
    case Players006 = 6;
    case Players007 = 7;
    case Players008 = 8;
    case Players009 = 9;
}
