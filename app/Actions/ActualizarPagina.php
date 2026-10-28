<?php

namespace App\Actions;

use App\Models\Pagina;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ActualizarPagina
{
    /**
     * Guarda el texto de una página. La foto nueva sustituye a la anterior y borra su fichero;
     * con $quitarFoto se elimina sin poner otra.
     *
     * @param  array<string, mixed>  $data  datos ya validados por UpdatePaginaRequest
     */
    public function handle(Pagina $pagina, array $data, ?UploadedFile $foto = null, bool $quitarFoto = false): Pagina
    {
        $anterior = $pagina->foto;
        unset($data['foto'], $data['quitar_foto']);

        if ($foto) {
            $data['foto'] = basename((string) $foto->store('paginas', 'public'));
        } elseif ($quitarFoto) {
            $data['foto'] = null;
        }

        $pagina->update($data);

        if ($anterior && $anterior !== $pagina->foto) {
            Storage::disk('public')->delete('paginas/'.$anterior);
        }

        return $pagina;
    }
}
