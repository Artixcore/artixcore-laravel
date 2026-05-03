<?php

namespace App\Http\Requests\Admin;

use App\Models\CrmContact;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrmContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if ($user === null) {
            return false;
        }

        $contact = $this->route('crm_contact');
        if ($contact !== null) {
            return $user->can('update', $contact);
        }

        return $user->can('create', \App\Models\CrmContact::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(CrmContact::TYPES)],
            'status' => ['required', 'string', Rule::in(CrmContact::STATUSES)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'lowercase', 'email:rfc,dns', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'url', 'max:500'],
            'source_id' => ['nullable', 'integer', 'exists:crm_sources,id'],
            'source_detail' => ['nullable', 'string', 'max:500'],
            'service_interest' => ['nullable', 'string', 'max:255'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'saas_platform_id' => ['nullable', 'integer', 'exists:products,id'],
            'project_id' => ['nullable', 'integer', 'exists:crm_projects,id'],
            'industry' => ['nullable', 'string', 'max:120'],
            'company_size' => ['nullable', 'string', 'max:80'],
            'budget_range' => ['nullable', 'string', 'max:80'],
            'priority' => ['required', 'string', Rule::in(CrmContact::PRIORITIES)],
            'notes' => ['nullable', 'string', 'max:20000'],
            'next_follow_up_at' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
