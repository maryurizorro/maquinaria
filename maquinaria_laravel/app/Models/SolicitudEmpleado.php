<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
