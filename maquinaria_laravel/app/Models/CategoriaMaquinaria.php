<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
