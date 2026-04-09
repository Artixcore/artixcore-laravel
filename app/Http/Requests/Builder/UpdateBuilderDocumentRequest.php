<?php

namespace App\Http\Requests\Builder;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBuilderDocumentRequest extends FormRequest
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
            'document' => ['required', 'array'],
            'base_version_id' => ['nullable', 'integer', 'min:1'],
            'label' => ['sometimes', 'string', 'max:32'],
        ];
    }
}
