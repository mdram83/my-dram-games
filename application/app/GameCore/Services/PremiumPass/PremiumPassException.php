<?php

namespace App\GameCore\Services\PremiumPass;

class PremiumPassException extends \Exception
{
    public const MESSAGE_MISSING_PREMIUM_PASS = 'Premium access required';
}
