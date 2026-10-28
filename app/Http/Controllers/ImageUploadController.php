<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Sin SVG (puede llevar JavaScript) y con nombre aleatorio y extensión según el contenido real
        $path = $request->file('image')->store('imagenes', 'public');

        return response()->json([
            'message' => 'Imagen subida correctamente',
            'url' => Storage::disk('public')->url($path),
        ]);
    }
}
