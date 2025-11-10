<?php

namespace App\Http\Requests\GameCore;

use App\Http\Controllers\ControllerValidationException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class GamePlayDisconnectionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'disconnected' => 'required|string|max:255',
        ];
    }

    /**
     * @throws ControllerValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ControllerValidationException(ControllerValidationException::MESSAGE_INCORRECT_INPUTS);
    }

    public function getDisconnectedPlayerName(): string
    {
        return $this->get('disconnected');
    }
}
