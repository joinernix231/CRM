<?php

namespace App\Repositories;

use App\Models\Client;

class ClientRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'search',
        'name',
        'status',
    ];

    protected array $searchColumns = [
        'name',
        'company',
        'email',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function getSearchColumns(): array
    {
        return $this->searchColumns;
    }

    public function model(): string
    {
        return Client::class;
    }
}
