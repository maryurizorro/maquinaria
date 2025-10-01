<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="Empleado",
 *     type="object",
 *     title="Empleado",
 *     description="Modelo que representa un empleado",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nombre", type="string", example="Juan"),
 *     @OA\Property(property="apellido", type="string", example="Pérez"),
 *     @OA\Property(property="documento", type="string", example="12345678"),
 *     @OA\Property(property="email", type="string", example="juan.perez@example.com"),
 *     @OA\Property(property="direccion", type="string", example="Calle 123"),
 *     @OA\Property(property="telefono", type="string", example="3001234567"),
 *     @OA\Property(property="rol", type="string", example="Administrador")
 * )
 */
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
