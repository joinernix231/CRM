<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'email',
        'phone',
        'position',
        'is_primary',
    ];

    public $rules = [
        'client_id' => 'required|exists:clients,id',
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:contacts,email',
        'phone' => 'required|string|max:255',
        'position' => 'required|string|max:255',
        'is_primary' => 'required|boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
