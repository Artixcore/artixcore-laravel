<?php

namespace App\Http\Requests\Builder;

use Illuminate\Foundation\Http\FormRequest;

class PublishBuilderPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('builder.publish') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}
