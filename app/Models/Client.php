<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'company',
        'status',
    ];

    public static array $rules = [
        'user_id' => 'required|integer|exists:users,id',
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|string|max:255',
        'company' => 'required|string|max:255',
    ];

    public static array $customAttributes = [
        'user_id' => 'usuario',
        'name' => 'nombre',
        'email' => 'email',
        'phone' => 'teléfono',
        'company' => 'empresa',
        'status' => 'estado',
        'id' => 'cliente',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function primaryContact()
    {
        return $this->hasOne(Contact::class)->where('is_primary', true);
    }
}
