<?php

namespace App\Http\Requests\Builder;

use Illuminate\Foundation\Http\FormRequest;

class AiBuilderProposeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('builder.ai.use') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'prompt' => ['required', 'string', 'max:8000'],
            'document' => ['required', 'array'],
            'target_node_id' => ['nullable', 'string', 'max:64'],
        ];
    }
}
