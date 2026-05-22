<?php

namespace App\Http\Requests\Api\Contact;

use App\Models\Contact;

class UpdateContactAPIRequest extends ContactAPIRequest
{
    public function validationData(): array
    {
        $this->mergeClientRouteId();
        $this->mergeContactRouteId();
        $this->mergeUserSessionId();

        return $this->all();
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = Contact::$rules;
        $rules['id'] = $this->contactIdRules()['id'];
        $rules['client_id'] = $this->clientIdRules()['client_id'];
        $rules['name'] = ['sometimes', 'required', 'string', 'max:255'];
        $rules['email'] = [
            'sometimes',
            'required',
            'email',
            $this->uniqueRule('contacts', 'email')->ignore($this->route('contact')),
        ];
        $rules['phone'] = ['sometimes', 'required', 'integer', 'max_digits:10'];
        $rules['position'] = ['sometimes', 'required', 'string', 'max:255'];
        $rules['is_primary'] = ['sometimes', 'required', 'boolean'];

        return $rules;
    }
}
