<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="Empresa",
 *     type="object",
 *     title="Empresa",
 *     description="Modelo de Empresa con sus relaciones",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nombre", type="string", example="Constructora XYZ"),
 *     @OA\Property(property="nit", type="string", example="900123456-7"),
 *     @OA\Property(property="direccion", type="string", example="Calle 123 #45-67"),
 *     @OA\Property(property="telefono", type="string", example="3001234567"),
 *     @OA\Property(property="email", type="string", example="contacto@xyz.com"),
 *     @OA\Property(
 *         property="representantes",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Representante")
 *     ),
 *     @OA\Property(
 *         property="solicitudes",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Solicitud")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-30T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-30T12:34:56Z")
 * )
 */
class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'nit',
        'direccion',
        'telefono',
        'email',
        'ciudad',
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
