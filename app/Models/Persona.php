<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'persona';

    protected $fillable = [
        'user_id',
        'cedula',
        'fecha_nacimiento',
        'direccion',
        'telefono',
    ];

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
