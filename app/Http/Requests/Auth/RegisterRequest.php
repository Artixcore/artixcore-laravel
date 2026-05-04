<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email:rfc', 'max:190', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'company_name' => ['nullable', 'string', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'terms' => ['accepted'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson() || $this->header('X-Requested-With') === 'XMLHttpRequest') {
            throw new HttpResponseException(
                response()->json([
                    'ok' => false,
                    'message' => 'Please check the form and try again.',
                    'errors' => $validator->errors(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
