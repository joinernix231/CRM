<?php

namespace App\Http\Requests\Api\Contact;

use App\Http\Requests\APIRequest;
use App\Models\Client;
use App\Models\Contact;
use App\Rules\ObjectBelongsToModelRule;

abstract class ContactAPIRequest extends APIRequest
{
    protected function mergeClientRouteId(): void
    {
        $this->addParametersToRequest(['client_id' => $this->route('client')]);
    }

    protected function mergeUserSessionId(): void
    {
        $this->addParametersToRequest(['user_id' => session('user_id')]);
    }

    protected function mergeContactRouteId(): void
    {
        $this->addParametersToRequest(['id' => $this->route('contact')]);
    }

    protected function clientIdRules(): array
    {
        return [
            'client_id' => [
                'required',
                'integer',
                $this->existsRule('clients'),
                new ObjectBelongsToModelRule(session('user'), 'clients', 'usuario'),
            ],
        ];
    }

    protected function contactIdRules(): array
    {
        $client = Client::where('user_id', session('user_id'))
            ->find($this->route('client'));

        return [
            'id' => [
                'required',
                'integer',
                $this->existsRule('contacts'),
                new ObjectBelongsToModelRule($client, 'contacts', 'cliente'),
            ],
        ];
    }

    public function attributes(): array
    {
        return Contact::$customAttributes;
    }
}
