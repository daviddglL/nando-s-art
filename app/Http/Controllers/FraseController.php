<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

// Frase del día de la portada. Se cachea hasta medianoche y no deja la página colgada si el servicio externo falla.
class FraseController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $frase = Cache::get('frase-del-dia');

        if (! $frase) {
            try {
                $response = Http::timeout(3)->get('https://frasedeldia.azurewebsites.net/api/phrase');
                if ($response->successful() && $response->json('phrase')) {
                    $frase = $response->json();
                    Cache::put('frase-del-dia', $frase, now()->endOfDay());
                }
            } catch (HttpClientException $e) {
                report($e);
            }
        }

        return $frase
            ? response()->json($frase)
            : response()->json(['message' => 'No se pudo cargar la frase del día'], 503);
    }
}
