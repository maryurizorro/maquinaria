<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @OA\Schema(
 *     schema="CategoriaMaquinaria",
 *     type="object",
 *     title="Categoría de Maquinaria",
 *     required={"nombre"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nombre", type="string", example="Excavadoras"),
 *     @OA\Property(property="descripcion", type="string", example="Máquinas para excavar"),
 *     @OA\Property(
 *         property="tiposMaquinaria",
 *         type="array",
 *         @OA\Items(type="object")
 *     )
 * )
 */
class CategoriaMaquinaria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function tiposMaquinaria()
    {
        return $this->hasMany(TipoMaquinaria::class, 'categoria_id');
    }
}
