<?php

namespace App\Http\Requests\Master;

use App\Models\AdminAccessRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminAccessRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('security.manage') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $patch = $this->isMethod('PATCH');

        return [
            'name' => ['nullable', 'string', 'max:120'],
            'guard_area' => [
                $patch ? 'sometimes' : 'required',
                'string',
                Rule::in(AdminAccessRule::guardAreas()),
            ],
            'ip_address' => ['sometimes', 'nullable', 'string', 'max:45', 'ip'],
            'cidr' => ['sometimes', 'nullable', 'string', 'max:49', 'regex:/^([0-9a-fA-F:.]+)\/([0-9]|[1-9][0-9]|1[0-1][0-9]|12[0-8])$/'],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v): void {
            $ip = $this->input('ip_address');
            $cidr = $this->input('cidr');
            if ($ip && $cidr) {
                $v->errors()->add('ip_address', 'Provide either a single IP or a CIDR range, not both.');
            }

            if ($this->isMethod('POST')) {
                if (! $ip && ! $cidr) {
                    $v->errors()->add('ip_address', 'Provide a single IP or a CIDR range.');
                }
            }

            if ($cidr && ! $v->errors()->has('cidr')) {
                $parts = explode('/', $cidr, 2);
                if (count($parts) === 2) {
                    [$addr, $mask] = $parts;
                    if (filter_var($addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        $m = (int) $mask;
                        if ($m < 0 || $m > 32) {
                            $v->errors()->add('cidr', 'Invalid IPv4 CIDR mask.');
                        }
                    } elseif (filter_var($addr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                        $m = (int) $mask;
                        if ($m < 0 || $m > 128) {
                            $v->errors()->add('cidr', 'Invalid IPv6 CIDR mask.');
                        }
                    }
                }
            }
        });
    }
}
