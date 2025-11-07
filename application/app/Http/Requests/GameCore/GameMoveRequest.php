<?php

namespace App\Http\Requests\GameCore;

use App\Http\Controllers\ControllerValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class GameMoveRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'move' => 'required|array',
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
