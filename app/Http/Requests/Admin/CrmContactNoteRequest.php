<?php

namespace App\Http\Requests\Admin;

use App\Models\CrmContactNote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrmContactNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $contact = $this->route('crm_contact');

        return $contact !== null && $this->user()?->can('update', $contact) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(CrmContactNote::TYPES)],
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:20000'],
        ];
    }
}
