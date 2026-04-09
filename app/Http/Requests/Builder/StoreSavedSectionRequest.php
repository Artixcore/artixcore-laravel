<?php

namespace App\Http\Requests\Builder;

use Illuminate\Foundation\Http\FormRequest;

class StoreSavedSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('builder.access') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'array'],
        ];
    }
}
