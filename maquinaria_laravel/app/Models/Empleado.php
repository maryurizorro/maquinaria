<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'documento',
        'email',
        'direccion',
        'telefono',
        'rol',
    ];

    public function solicitudes()
    {
        return $this->belongsToMany(Solicitud::class, 'solicitud_empleados')
                    ->withPivot('estado')
                    ->withTimestamps();
    }
}
