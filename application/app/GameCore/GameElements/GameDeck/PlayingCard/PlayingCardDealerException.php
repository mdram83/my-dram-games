<?php

namespace App\GameCore\GameElements\GameDeck\PlayingCard;

use Exception;

class PlayingCardDealerException extends Exception
{
    public const MESSAGE_DISTRIBUTION_DEFINITION = 'Incorrect distribution definition';
    public const MESSAGE_COLLECTION_FROM_INVALID = 'Invalid stock to collect from';
    public const MESSAGE_NOT_ENOUGH_TO_DEAL = 'Not enough cards in deck to deal according to definition';
    public const MESSAGE_NOT_ENOUGH_IN_STOCK = 'Not enough cards in requested stock';
    public const MESSAGE_NOT_UNIQUE_KEYS = 'Requested keys are not unique';
    public const MESSAGE_KEY_MISSING_IN_STOCK = 'Requested keys missing in stock';
    public const MESSAGE_KEYS_NOT_MATCHING_STOCK = 'Requested keys not matching stock';
    public const MESSAGE_COMBINATION_INVALID = 'Combination format invalid';
}
