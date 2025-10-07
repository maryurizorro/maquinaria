<?php

namespace App\Http\Controllers;

use App\Models\DetalleSolicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubirImagenController extends Controller
{
    /**
     * Subir una imagen asociada a una solicitud.
     *
     * @group Imágenes
     * 
     * Este endpoint permite subir una imagen relacionada con una solicitud.
     * 
     * @bodyParam Url_foto file required Imagen a subir (formatos permitidos: jpeg, png, jpg, gif). Máximo 2 MB.
     * @bodyParam solicitud_id integer required ID de la solicitud asociada. Ejemplo: 1.
     * 
     * @response 200 {
     *   "status": true,
     *   "message": "Imagen subida correctamente",
     *   "url": "/storage/DetalleSolicitud/1/imagen.jpg"
     * }
     * 
     * @response 422 {
     *   "status": false,
     *   "message": "Error de validación",
     *   "errors": {
     *     "Url_foto": ["El campo Url_foto es obligatorio."],
     *     "solicitud_id": ["El campo solicitud_id es obligatorio."]
     *   }
     * }
     */
    public function subirImagen(Request $request)
    {
        $request->validate([
            'Url_foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'solicitud_id' => 'required|exists:solicituds,id',
        ]);

        $carpeta = "DetalleSolicitud/{$request->solicitud_id}";
        $path = $request->file('Url_foto')->store($carpeta, 'public');

        return response()->json([
            'status' => true,
            'message' => 'Imagen subida correctamente',
            'url' => Storage::url($path)
        ]);
    }

    /**
     * Eliminar una imagen por su URL.
     *
     * @group Imágenes
     * 
     * Permite eliminar una imagen previamente subida a partir de su URL pública.
     * 
     * @queryParam urlImagen string required URL de la imagen a eliminar. Ejemplo: /storage/DetalleSolicitud/1/imagen.jpg.
     * 
     * @response 200 {
     *   "status": true,
     *   "message": "Imagen eliminada exitosamente"
     * }
     * 
     * @response 404 {
     *   "status": false,
     *   "message": "Imagen no encontrada"
     * }
     */
    public function EliminarImagen(?string $urlImagen)
    {
        if (!$urlImagen) {
            return response()->json([
                'status' => false,
                'message' => 'URL de imagen no proporcionada'
            ], 422);
        }

        // Extraer la ruta después de /storage/
        $path = Str::after($urlImagen, '/storage/');

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return response()->json([
                'status' => true,
                'message' => 'Imagen eliminada exitosamente'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Imagen no encontrada'
        ], 404);
    }
}
