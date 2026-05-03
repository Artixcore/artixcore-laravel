<?php

namespace App\Http\Requests\Admin;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $lead = $this->route('lead');

        return $lead instanceof Lead
            && ($this->user()?->can('update', $lead) ?? false);
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('assigned_to') === '') {
            $this->merge(['assigned_to' => null]);
        }
        if ($this->input('reviewed_by') === '') {
            $this->merge(['reviewed_by' => null]);
        }
        if ($this->input('reviewed_at') === '') {
            $this->merge(['reviewed_at' => null]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(Lead::statuses())],
            'internal_notes' => ['nullable', 'string', 'max:50000'],
            'conversation_summary' => ['nullable', 'string', 'max:50000'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'company' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'string', 'max:255'],
            'service_interest' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:10000'],
            'message' => ['nullable', 'string', 'max:5000'],
            'service_type' => ['nullable', 'string', 'max:120'],
            'admin_notes' => ['nullable', 'string', 'max:50000'],
            'reviewed_at' => ['nullable', 'date'],
            'reviewed_by' => ['nullable', 'integer', 'exists:users,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
