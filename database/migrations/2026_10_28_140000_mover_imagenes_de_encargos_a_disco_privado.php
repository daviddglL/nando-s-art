<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// Las imágenes de referencia de los presupuestos estaban en el disco public (cualquiera con la URL las veía).
// Se mueven al disco privado; desde ahora solo se sirven por /presupuestos/{id}/imagen.
return new class extends Migration
{
    public function up(): void
    {
        $this->mover('public', 'local');
    }

    public function down(): void
    {
        $this->mover('local', 'public');
    }

    private function mover(string $origen, string $destino): void
    {
        $desde = Storage::disk($origen);
        $hacia = Storage::disk($destino);

        DB::table('encargos')->whereNotNull('imagen')->pluck('imagen')->each(function (string $ruta) use ($desde, $hacia) {
            if (! $desde->exists($ruta)) {
                return;
            }

            $hacia->writeStream($ruta, $desde->readStream($ruta));
            $desde->delete($ruta);
        });
    }
};
