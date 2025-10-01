<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="TipoMaquinaria",
 *     type="object",
 *     title="Tipo de Maquinaria",
 *     required={"nombre","categoria_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nombre", type="string", example="Excavadora"),
 *     @OA\Property(property="descripcion", type="string", example="Equipo de construcción usado para excavar"),
 *     @OA\Property(property="categoria_id", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-30T14:48:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-30T14:48:00.000000Z")
 * )
 */
class TipoMaquinaria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaMaquinaria::class, 'categoria_id');
    }

    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class);
    }
}
