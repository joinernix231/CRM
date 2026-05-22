<?php

namespace App\Http\Requests\Api\Client;

use App\Models\Client;

class UpdateClientAPIRequest extends ClientAPIRequest
{
    public function validationData(): array
    {
        $this->mergeClientRouteId();
        $this->addParametersToRequest(['user_id' => session('user_id')]);

        return $this->all();
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = Client::$rules;
        $rules['id'] = $this->clientIdRules()['id'];
        $rules['name'] = ['sometimes', 'required', 'string', 'max:255'];
        $rules['email'] = [
            'sometimes',
            'required',
            'email',
            $this->uniqueRule('clients', 'email')->ignore($this->route('client')),
        ];
        $rules['phone'] = ['sometimes', 'required', 'integer', 'max_digits:10'];
        $rules['company'] = ['sometimes', 'required', 'string', 'max:255'];
        $rules['status'] = ['sometimes', 'required', 'in:active,inactive,prospect'];
        $rules['user_id'] = ['required', 'integer', 'exists:users,id'];

        return $rules;
    }
}
