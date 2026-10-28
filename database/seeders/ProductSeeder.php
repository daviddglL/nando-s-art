<?php

namespace Database\Seeders;

use App\Actions\GenerarMiniaturas;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // Imágenes de las obras de ejemplo (versionadas en el repo). Se pueden sustituir por las fotos reales con el mismo nombre.
    public const CARPETA_IMAGENES = __DIR__.'/imagenes';

    public function run(GenerarMiniaturas $miniaturas): void
    {
        // Con el modelo (y no DB::table) para que cada obra reciba su slug y su posición
        $obras = [
            [
                'name' => 'Casa Embrujada',
                'description' => 'Esta ilustración destaca por su estilo surrealista y macabro. La casa toma rasgos antropomórficos con enormes ojos inyectados de sangre y una boca abierta en forma de puerta, lo que genera una sensación de inquietud y vida monstruosa.',
                'height' => 30,
                'width' => 40,
                'style' => 'acuarela',
                'category' => 'urbano',
                'imagen' => 'casa-embrujada.webp',
            ],
            [
                'name' => 'Caseta Rústica',
                'description' => 'Esta obra transmite un aire nostálgico y tranquilo. La caseta está representada con líneas precisas, pero su fachada desgastada y la vegetación que la rodea le otorgan un encanto melancólico.',
                'height' => 25,
                'width' => 35,
                'style' => 'acuarela',
                'category' => 'urbano',
                'imagen' => 'caseta.webp',
            ],
            [
                'name' => 'Casa en la Isla',
                'description' => 'Esta acuarela tiene un carácter poético y onírico. La composición se equilibra entre la delicadeza de los colores pastel y la nitidez de las líneas arquitectónicas.',
                'height' => 30,
                'width' => 40,
                'style' => 'acuarela',
                'category' => 'paisaje',
                'imagen' => 'acuarela-isla.webp',
            ],
            [
                'name' => 'Atardecer en la atalaya',
                'description' => 'Esta obra captura la esencia de un atardecer en un entorno natural. La atalaya, con su estructura robusta y detalles arquitectónicos, se erige como un punto focal en la composición.',
                'height' => 30,
                'width' => 40,
                'style' => 'acuarela',
                'category' => 'paisaje',
                'imagen' => 'acuarela-japon.webp',
            ],
            [
                'name' => 'Masión en el bosque',
                'description' => 'Esta ilustración evoca un ambiente de misterio y encanto. La mansión, con su arquitectura detallada y elementos góticos, se sitúa en un entorno forestal denso. Los árboles que la rodean añaden un aire de aislamiento y secreto, mientras que los colores oscuros y sombríos transmiten una sensación de melancolía.',
                'height' => 30,
                'width' => 40,
                'style' => 'acuarela',
                'category' => 'urbano',
                'imagen' => 'acuarela-mansion.webp',
            ],
            [
                'name' => 'Transporte de la ciudad',
                'description' => 'Esta obra captura la esencia del transporte urbano con un enfoque dinámico y contemporáneo. La representación de vehículos en movimiento, combinada con una paleta de colores vibrantes, sugiere energía y ritmo de vida en la ciudad.',
                'height' => 30,
                'width' => 40,
                'style' => 'acuarela',
                'category' => 'urbano',
                'imagen' => 'acuarela-tren.webp',
            ],
        ];

        foreach ($obras as $obra) {
            // Se copian al disco public (storage/app/public/obras) para que la galería no salga sin fotos
            $destino = 'obras/'.$obra['imagen'];
            if (! Storage::disk('public')->exists($destino)) {
                Storage::disk('public')->put($destino, (string) file_get_contents(self::CARPETA_IMAGENES.'/'.$obra['imagen']));
            }

            $miniaturas->handle(Product::create($obra));
        }
    }
}
