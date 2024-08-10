<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Config;
use Illuminate\Translation\PotentiallyTranslatedString;

class BetaRegistrationCode implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value != Config::get('auth.beta_registration_code')) {
            $fail('Incorrect Registration Code');
        }
    }
}
