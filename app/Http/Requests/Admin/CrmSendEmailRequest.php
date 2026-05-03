<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CrmSendEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        $contact = $this->route('crm_contact');

        return $contact !== null && $this->user()?->can('email', $contact) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:190'],
            'body' => ['required', 'string', 'max:10000'],
            'template_id' => ['nullable', 'integer', 'exists:crm_email_templates,id'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $contact = $this->route('crm_contact');
            $email = is_object($contact) ? $contact->email : null;
            if (! is_string($email) || $email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $v->errors()->add('email', __('Contact does not have a valid email address.'));
            }
        });
    }
}
