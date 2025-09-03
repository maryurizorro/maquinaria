<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
