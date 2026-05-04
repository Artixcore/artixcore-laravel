<?php

namespace App\Http\Requests\Admin;

use App\Services\HomepageContentResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreHomepageSectionItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $pick = $this->input('pick');
        if (is_string($pick) && preg_match('/^([a-z][a-z0-9_]*):(\d+)$/i', $pick, $m)) {
            $this->merge([
                'item_type' => $m[1],
                'item_id' => (int) $m[2],
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $types = array_keys(HomepageContentResolver::ITEM_TYPES);

        return [
            'pick' => ['nullable', 'string', 'max:120'],
            'item_type' => ['required', 'string', 'max:100', Rule::in($types)],
            'item_id' => ['required', 'integer', 'min:1'],
            'title_override' => ['nullable', 'string', 'max:255'],
            'description_override' => ['nullable', 'string', 'max:10000'],
            'image_override' => ['nullable', 'string', 'max:2048'],
            'button_text' => ['nullable', 'string', 'max:255'],
            'button_url' => ['nullable', 'string', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $type = $this->input('item_type');
            $id = (int) $this->input('item_id', 0);
            if (! is_string($type) || $id < 1) {
                return;
            }
            $class = HomepageContentResolver::ITEM_TYPES[$type] ?? null;
            if ($class === null || ! $class::query()->whereKey($id)->exists()) {
                $v->errors()->add('item_id', __('The selected item does not exist.'));
            }
        });
    }
}
