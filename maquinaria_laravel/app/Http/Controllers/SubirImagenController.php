<?php

namespace App\Http\Controllers;

use App\Models\DetalleSolicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubirImagenController extends Controller
{
     /**
     * @OA\Post(
     *     path="/api/imagenes/subir",
     *     summary="Subir una imagen asociada a una solicitud",
     *     tags={"Imagenes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"Url_foto","solicitud_id"},
     *                 @OA\Property(
     *                     property="Url_foto",
     *                     type="string",
     *                     format="binary",
     *                     description="Imagen a subir (jpeg, png, jpg, gif)"
     *                 ),
     *                 @OA\Property(
     *                     property="solicitud_id",
     *                     type="integer",
     *                     description="ID de la solicitud asociada",
     *                     example=1
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Imagen subida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Imagen subida correctamente"),
     *             @OA\Property(property="url", type="string", example="/storage/DetalleSolicitud/1/imagen.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
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
  /**
     * @OA\Delete(
     *     path="/api/imagenes/eliminar",
     *     summary="Eliminar una imagen por URL",
     *     tags={"Imagenes"},
     *     @OA\Parameter(
     *         name="urlImagen",
     *         in="query",
     *         required=true,
     *         description="URL de la imagen a eliminar (ejemplo: /storage/DetalleSolicitud/1/imagen.jpg)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Imagen eliminada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Imagen eliminada exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Imagen no encontrada"
     *     )
     * )
     */
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