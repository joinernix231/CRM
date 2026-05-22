<?php

namespace App\Http\Requests\Api\Contact;

class ShowContactAPIRequest extends ContactAPIRequest
{
    public function validationData(): array
    {
        $this->mergeClientRouteId();
        $this->mergeContactRouteId();

        return $this->all();
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge($this->clientIdRules(), $this->contactIdRules());
    }
}
