<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    protected $fillable = [
        'user_id',
        'client_id',
        'name',
        'email',
        'phone',
        'position',
        'is_primary',
    ];

    public static array $rules = [
        'user_id' => 'required|integer|exists:users,id',
        'client_id' => 'required|integer|exists:clients,id',
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|string|max:255',
        'position' => 'required|string|max:255',
        'is_primary' => 'required|boolean',
    ];

    public static array $customAttributes = [
        'user_id' => 'usuario',
        'client_id' => 'cliente',
        'name' => 'nombre',
        'email' => 'email',
        'phone' => 'teléfono',
        'position' => 'cargo',
        'is_primary' => 'contacto principal',
        'id' => 'contacto',
    ];

    protected static function booted(): void
    {
        static::saving(function (Contact $contact) {
            if (! $contact->is_primary) {
                return;
            }

            static::query()
                ->where('client_id', $contact->client_id)
                ->when($contact->exists, fn ($query) => $query->where('id', '!=', $contact->id))
                ->where('is_primary', true)
                ->update(['is_primary' => false]);
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
