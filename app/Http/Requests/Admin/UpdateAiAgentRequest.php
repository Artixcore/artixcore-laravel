<?php

namespace App\Http\Requests\Admin;

use App\Models\AiAgent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAiAgentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $agent = $this->route('ai_agent');

        return $agent instanceof AiAgent
            && ($this->user()?->can('update', $agent) ?? false);
    }

    protected function prepareForValidation(): void
    {
        foreach (['languages_json', 'lead_capture_schema_json', 'escalation_rules_json', 'availability_json', 'tools_allowed_json'] as $key) {
            if ($this->input($key) === '') {
                $this->merge([$key => null]);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $agent = $this->route('ai_agent');
        $id = $agent instanceof AiAgent ? $agent->getKey() : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/', Rule::unique('ai_agents', 'slug')->ignore($id)],
            'instructions' => ['nullable', 'string', 'max:50000'],
            'model_id' => ['nullable', 'string', 'max:255'],
            'default_ai_provider_id' => ['nullable', 'integer', 'exists:ai_providers,id'],
            'role_label' => ['nullable', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'business_description' => ['nullable', 'string', 'max:10000'],
            'business_goals' => ['nullable', 'string', 'max:10000'],
            'tone' => ['nullable', 'string', 'max:255'],
            'response_style' => ['nullable', 'string', 'max:255'],
            'languages_json' => ['nullable', 'string', 'max:5000', 'json'],
            'forbidden_topics' => ['nullable', 'string', 'max:10000'],
            'lead_capture_schema_json' => ['nullable', 'string', 'max:10000', 'json'],
            'escalation_rules_json' => ['nullable', 'string', 'max:10000', 'json'],
            'availability_json' => ['nullable', 'string', 'max:10000', 'json'],
            'focus' => ['required', 'string', Rule::in(['sales', 'support', 'general'])],
            'tools_allowed_json' => ['nullable', 'string', 'max:10000', 'json'],
            'status' => ['required', 'string', Rule::in(['active', 'disabled'])],
        ];
    }
}
