<?php

namespace App\Http\Controllers;

use App\Models\DetalleSolicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubirImagenController extends Controller
{
    public function subirImagen(Request $request)
    {
        // Validar que se haya enviado una imagen
        $request->validate(([
            'Url_foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'solicitud_id' => 'required|exists:solicituds,id',
        ]));

        $carpeta = "DetalleSolicitud/{$request->solicitud_id}";

        $path = $request->file(key: 'Url_foto')->store($carpeta, options: 'public');

        return Storage::url($path);
    }

public function EliminarImagen(?string $urlImagen)
{
    if ($urlImagen) {
        // Extraer la ruta del archivo después de '/storage/'
        $path = Str::after($urlImagen, '/storage/');

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
}