<?php

namespace App\Http\Requests\Api\Client;

use App\Http\Requests\Api\Client\Concerns\BuildsClientListFilters;

class ReadClientAPIRequest extends ClientAPIRequest
{
    use BuildsClientListFilters;

    protected function prepareForValidation(): void
    {
        $this->mergeClientListFilters();
    }

    public function validationData(): array
    {
        $this->addParametersToRequest([]);

        return $this->all();
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'nullable', 'string', 'in:active,inactive,prospect'],
            'filters' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return array_merge(parent::attributes(), [
            'search' => 'búsqueda',
            'status' => 'estado',
            'filters' => 'filtros',
        ]);
    }
}
