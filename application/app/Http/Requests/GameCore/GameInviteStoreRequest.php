<?php

namespace App\Http\Requests\GameCore;

use App\Http\Controllers\ControllerValidationException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class GameInviteStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'slug' => 'required|string|max:255',
            'options.numberOfPlayers' => 'required|integer|min:1',
            'options.autostart' => 'required|boolean',
        ];
    }

    /**
     * @throws ControllerValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $message = json_encode([
            'message' => ControllerValidationException::MESSAGE_INCORRECT_INPUTS,
            'errors' => $validator->errors()
        ]);
        throw new ControllerValidationException($message);
    }
}
