<?php

namespace App\Services\PremiumPass;

class PremiumPassException extends \Exception
{
    public const string MESSAGE_MISSING_PREMIUM_PASS = 'Premium access required';
}
