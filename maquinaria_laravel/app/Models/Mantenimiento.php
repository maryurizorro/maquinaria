<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
/**
 * @OA\Schema(
 *     schema="maquinaria_laravel",
 *     type="object",
 *     title="Maquinaria",
 *     description="Esquema de una maquinaria",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nombre", type="string", example="Tractor"),
 *     @OA\Property(property="descripcion", type="string", example="Maquinaria pesada"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-30T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-30T10:00:00Z")
 * )
 */

class Mantenimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'costo',
        'tiempo_estimado',
        'manual_procedimiento',
        'tipo_maquinaria_id',
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'tiempo_estimado' => 'integer',
    ];

    public function tipoMaquinaria()
    {
        return $this->belongsTo(TipoMaquinaria::class, 'tipo_maquinaria_id');
    }

    public function detallesSolicitud()
    {
        return $this->hasMany(DetalleSolicitud::class);
    }

    /**
     * Reglas de validación para asegurar que un mantenimiento
     * no se pueda aplicar a diferentes tipos de maquinaria
     */
    public static function rules()
    {
        return [
            'codigo' => [
                'required',
                'string',
                'max:50',
                Rule::unique('mantenimientos', 'codigo')->where(function ($query) {
                    return $query->where('tipo_maquinaria_id', request('tipo_maquinaria_id'));
                })->ignore(request('id'))
            ],
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'costo' => 'required|numeric|min:0',
            'tiempo_estimado' => 'required|integer|min:1',
            'manual_procedimiento' => 'required|string',
            'tipo_maquinaria_id' => 'required|exists:tipo_maquinarias,id'
        ];
    }

    /**
     * Mensajes de validación personalizados
     */
    public static function messages()
    {
        return [
            'codigo.unique' => 'Ya existe un mantenimiento con este código para el tipo de maquinaria seleccionado. Cada mantenimiento debe ser específico para un tipo de maquinaria.',
            'tipo_maquinaria_id.required' => 'Debe seleccionar un tipo de maquinaria para el mantenimiento.',
            'tipo_maquinaria_id.exists' => 'El tipo de maquinaria seleccionado no existe.',
            'tiempo_estimado.required' => 'El tiempo estimado es obligatorio.',
            'tiempo_estimado.integer' => 'El tiempo estimado debe ser un número entero.',
            'tiempo_estimado.min' => 'El tiempo estimado debe ser al menos 1 hora.',
            'manual_procedimiento.required' => 'El manual de procedimiento es obligatorio.'
        ];
    }

    /**
     * Verificar si ya existe un mantenimiento con el mismo código
     * para un tipo de maquinaria diferente
     */
    public static function existeMantenimientoEnOtroTipo($codigo, $tipoMaquinariaId, $excludeId = null)
    {
        $query = self::where('codigo', $codigo)
                    ->where('tipo_maquinaria_id', '!=', $tipoMaquinariaId);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
}
