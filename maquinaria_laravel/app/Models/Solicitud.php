<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
