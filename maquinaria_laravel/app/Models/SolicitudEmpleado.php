<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="SolicitudEmpleado",
 *     type="object",
 *     title="SolicitudEmpleado",
 *     required={"solicitud_id", "empleado_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="solicitud_id", type="integer", example=1),
 *     @OA\Property(property="empleado_id", type="integer", example=2),
 *     @OA\Property(property="estado", type="string", enum={"asignado","en_proceso","completado"}, example="asignado"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-30T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-30T12:00:00Z")
 * )
 */

class SolicitudEmpleado extends Model
{
    use HasFactory;

    protected $fillable = [
        'solicitud_id',
        'empleado_id',
        'estado',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
