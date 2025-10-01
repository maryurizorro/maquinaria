<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'nit',
        'direccion',
        'telefono',
        'email',
    ];

    public function representantes()
    {
        return $this->hasMany(Representante::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }
}
