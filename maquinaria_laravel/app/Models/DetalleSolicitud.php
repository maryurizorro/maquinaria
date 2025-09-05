<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
