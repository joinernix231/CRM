<?php

namespace App\Http\Requests\Api\Client;

class ShowClientAPIRequest extends ClientAPIRequest
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
        return $this->clientIdRules();
    }
}
