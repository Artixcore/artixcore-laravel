<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('faqs.manage');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:20000'],
            'category' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'is_featured' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
            'show_on_general_faq' => ['sometimes', 'boolean'],
            'show_on_saas_page' => ['sometimes', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
