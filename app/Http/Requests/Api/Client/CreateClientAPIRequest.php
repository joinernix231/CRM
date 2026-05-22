<?php

namespace App\Http\Requests\Api\Client;

use App\Models\Client;

class CreateClientAPIRequest extends ClientAPIRequest
{
    public function validationData(): array
    {
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
        $rules['email'] = ['required', 'email', $this->uniqueRule('clients', 'email')];

        return $rules;
    }
}
