<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAnalyticsEventRequest extends FormRequest
{
    private const MAX_PAYLOAD_KEYS = 40;

    private const MAX_PAYLOAD_DEPTH = 4;

    private const MAX_PAYLOAD_JSON_CHARS = 8192;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'event_type' => ['required', 'string', 'max:120', 'regex:/^[a-zA-Z0-9][a-zA-Z0-9_.:-]*$/'],
            'session_id' => ['sometimes', 'nullable', 'string', 'max:120'],
            'payload' => ['sometimes', 'array', 'max:'.self::MAX_PAYLOAD_KEYS],
            'payload.*' => ['nullable'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $payload = $this->input('payload');
            if ($payload === null || ! is_array($payload)) {
                return;
            }

            $encoded = json_encode($payload);
            if (! is_string($encoded) || strlen($encoded) > self::MAX_PAYLOAD_JSON_CHARS) {
                $validator->errors()->add('payload', __('The payload is too large.'));

                return;
            }

            if ($this->arrayDepth($payload) > self::MAX_PAYLOAD_DEPTH) {
                $validator->errors()->add('payload', __('The payload structure is too deep.'));
            }
        });
    }

    /**
     * @param  array<mixed>  $array
     */
    private function arrayDepth(array $array, int $depth = 1): int
    {
        $max = $depth;
        foreach ($array as $value) {
            if (is_array($value)) {
                $max = max($max, $this->arrayDepth($value, $depth + 1));
            }
        }

        return $max;
    }
}
