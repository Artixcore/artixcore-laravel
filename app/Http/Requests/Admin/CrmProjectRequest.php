<?php

namespace App\Http\Requests\Admin;

use App\Models\CrmProject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrmProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if ($user === null || ! $user->can('crm.projects.manage')) {
            return false;
        }

        $project = $this->route('crm_project');
        if ($project !== null) {
            return $user->can('update', $project);
        }

        return $user->can('create', \App\Models\CrmProject::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $project = $this->route('project');

        return [
            'contact_id' => ['nullable', 'integer', 'exists:crm_contacts,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:191',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('crm_projects', 'slug')->ignore($project?->id),
            ],
            'status' => ['required', 'string', Rule::in(CrmProject::STATUSES)],
            'service_type' => ['nullable', 'string', 'max:120'],
            'budget_amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string', 'max:20000'],
            'internal_notes' => ['nullable', 'string', 'max:20000'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
