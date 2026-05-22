<?php

namespace App\Repositories;

use App\Models\Contact;
use Illuminate\Support\Facades\DB;

class ContactRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'name',
        'email',
        'phone',
        'position',
        'is_primary',
        'client_id',
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Contact::class;
    }

    public function create(array $attributes)
    {
        return DB::transaction(function () use ($attributes) {
            $contact = parent::create($attributes);

            if ($contact->is_primary) {
                $this->unsetOtherPrimaryContacts($contact);
            }

            return $contact->fresh();
        });
    }

    public function update(array $attributes, $id)
    {
        return DB::transaction(function () use ($attributes, $id) {
            $contact = parent::update($attributes, $id);
            $contact->refresh();

            if ($contact->is_primary) {
                $this->unsetOtherPrimaryContacts($contact);
            }

            return $contact->fresh();
        });
    }

    private function unsetOtherPrimaryContacts(Contact $contact): void
    {
        Contact::query()
            ->where('client_id', $contact->client_id)
            ->where('id', '!=', $contact->id)
            ->where('is_primary', true)
            ->update(['is_primary' => false]);
    }
}
