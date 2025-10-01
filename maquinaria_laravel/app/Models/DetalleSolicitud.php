<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="DetalleSolicitud",
 *     type="object",
 *     title="Detalle de Solicitud",
 *     description="Modelo que representa un detalle dentro de una solicitud de mantenimiento",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="solicitud_id", type="integer", example=10, description="ID de la solicitud relacionada"),
 *     @OA\Property(property="mantenimiento_id", type="integer", example=5, description="ID del mantenimiento relacionado"),
 *     @OA\Property(property="cantidad_maquinas", type="integer", example=3, description="Número de máquinas solicitadas"),
 *     @OA\Property(property="costo_total", type="number", format="float", example=1500.50, description="Costo total del detalle"),
 *     @OA\Property(property="Url_foto", type="string", example="uploads/imagenes/maquina1.jpg", description="Ruta de la imagen subida")
 * )
 */
class DetalleSolicitud extends Model
{
    use HasFactory;

    protected $fillable = [
        'solicitud_id',
        'mantenimiento_id',
        'cantidad_maquinas',
        'costo_total',
        'Url_foto',
    ];

    protected $casts = [
        'costo_total' => 'decimal:2',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class);
    }
}
