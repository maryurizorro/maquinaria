<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="Solicitud",
 *     type="object",
 *     required={"codigo","fecha_solicitud","empresa_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="codigo", type="string", example="SOL-2025-001"),
 *     @OA\Property(property="fecha_solicitud", type="string", format="date", example="2025-09-30"),
 *     @OA\Property(property="estado", type="string", example="pendiente"),
 *     @OA\Property(property="observaciones", type="string", example="Solicitud para mantenimiento general"),
 *     @OA\Property(property="descripcion_solicitud", type="string", example="Se requiere mantenimiento de maquinaria pesada"),
 *     @OA\Property(property="fecha_deseada", type="string", format="date", example="2025-10-15"),
 *     @OA\Property(property="empresa_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Solicitud extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'fecha_solicitud',
        'estado',
        'observaciones',
        'descripcion_solicitud',
        'fecha_deseada',
        'empresa_id',
    ];

    protected $casts = [
        'fecha_solicitud' => 'date',
        'fecha_deseada' => 'date',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function detallesSolicitud()
    {
        return $this->hasMany(DetalleSolicitud::class);
    }

    public function empleados()
    {
        return $this->belongsToMany(Empleado::class, 'solicitud_empleados')
                    ->withPivot('estado')
                    ->withTimestamps();
    }
}
