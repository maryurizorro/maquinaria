<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'costo',
        'tipo_maquinaria_id',
    ];

    protected $casts = [
        'costo' => 'decimal:2',
    ];

    public function tipoMaquinaria()
    {
        return $this->belongsTo(TipoMaquinaria::class, 'tipo_maquinaria_id');
    }

    public function detallesSolicitud()
    {
        return $this->hasMany(DetalleSolicitud::class);
    }
}
