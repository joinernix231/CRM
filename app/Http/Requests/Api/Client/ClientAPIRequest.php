<?php

namespace App\Http\Requests\Api\Client;

use App\Http\Requests\APIRequest;
use App\Rules\ObjectBelongsToModelRule;

abstract class ClientAPIRequest extends APIRequest
{
    protected function mergeClientRouteId(): void
    {
        $this->addParametersToRequest(['id' => $this->route('client')]);
    }

    protected function clientIdRules(): array
    {
        return [
            'id' => [
                'required',
                'integer',
                $this->existsRule('clients'),
                new ObjectBelongsToModelRule(session('user'), 'clients', 'usuario'),
            ],
        ];
    }

    public function attributes(): array
    {
        return \App\Models\Client::$customAttributes;
    }
}
