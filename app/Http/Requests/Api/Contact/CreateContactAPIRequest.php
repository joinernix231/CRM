<?php

namespace App\Http\Requests\Api\Contact;

use App\Models\Contact;

class CreateContactAPIRequest extends ContactAPIRequest
{
    public function validationData(): array
    {
        $this->mergeClientRouteId();
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
        $rules['client_id'] = $this->clientIdRules()['client_id'];
        $rules['email'] = ['required', 'email', $this->uniqueRule('contacts', 'email')];

        return $rules;
    }
}
