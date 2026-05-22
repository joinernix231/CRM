<?php

namespace App\Http\Requests\Api\Contact;

class ReadContactAPIRequest extends ContactAPIRequest
{
    public function validationData(): array
    {
        $this->mergeClientRouteId();

        return $this->all();
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge($this->clientIdRules(), [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);
    }
}
