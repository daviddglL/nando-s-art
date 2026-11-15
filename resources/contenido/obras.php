<?php

/*
|--------------------------------------------------------------------------
| Obras de la galería
|--------------------------------------------------------------------------
|
| Aquí está todo lo que sale en la galería: no hay base de datos ni panel.
| Para añadir una obra:
|   1. Copia su foto en public/obras/ (mejor .webp o .jpg, sin espacios en el nombre).
|   2. Copia uno de los bloques de abajo y cambia los datos.
|   3. (Opcional) Ejecuta `php artisan obras:miniaturas` para crear las versiones
|      pequeñas que cargan más rápido en el móvil.
|
| Las obras salen en la galería en el mismo orden que aquí.
|
| Campos:
|   slug         → la dirección de la ficha (/obras/casa-embrujada). Único, sin espacios ni tildes.
|   titulo       → nombre de la obra.
|   descripcion  → texto de la ficha. Puede tener varios párrafos.
|   tecnica      → acuarela, rotulador, oleo, sketch, papel o lienzo.
|   tema         → retrato, paisaje, abstracto, urbano u otro.
|   alto, ancho  → medidas en centímetros.
|   año          → (opcional) año en que se pintó.
|   precio       → (opcional) en euros, por ejemplo 150. Si no se pone sale «Precio a consultar».
|   vendida      → (opcional) true si ya está vendida.
|   destacada    → (opcional) true para que salga en la portada (como mucho 6).
|   imagen       → nombre del fichero de la foto principal dentro de public/obras/.
|   imagenes     → (opcional) más fotos de la misma obra (detalles, otros ángulos), también en public/obras/.
|
| Si algo está mal escrito (una técnica que no existe, una foto que falta, un slug repetido…)
| los tests lo avisan: `php artisan test`.
|
*/

return [
    [
        'slug' => 'casa-embrujada',
        'titulo' => 'Casa Embrujada',
        'descripcion' => 'Esta ilustración destaca por su estilo surrealista y macabro. La casa toma rasgos antropomórficos con enormes ojos inyectados de sangre y una boca abierta en forma de puerta, lo que genera una sensación de inquietud y vida monstruosa.',
        'tecnica' => 'acuarela',
        'tema' => 'urbano',
        'alto' => 30,
        'ancho' => 40,
        'destacada' => true,
        'imagen' => 'casa-embrujada.webp',
    ],
    [
        'slug' => 'caseta-rustica',
        'titulo' => 'Caseta Rústica',
        'descripcion' => 'Esta obra transmite un aire nostálgico y tranquilo. La caseta está representada con líneas precisas, pero su fachada desgastada y la vegetación que la rodea le otorgan un encanto melancólico.',
        'tecnica' => 'acuarela',
        'tema' => 'urbano',
        'alto' => 25,
        'ancho' => 35,
        'destacada' => true,
        'imagen' => 'caseta.webp',
    ],
    [
        'slug' => 'casa-en-la-isla',
        'titulo' => 'Casa en la Isla',
        'descripcion' => 'Esta acuarela tiene un carácter poético y onírico. La composición se equilibra entre la delicadeza de los colores pastel y la nitidez de las líneas arquitectónicas.',
        'tecnica' => 'acuarela',
        'tema' => 'paisaje',
        'alto' => 30,
        'ancho' => 40,
        'destacada' => true,
        'imagen' => 'acuarela-isla.webp',
    ],
    [
        'slug' => 'atardecer-en-la-atalaya',
        'titulo' => 'Atardecer en la atalaya',
        'descripcion' => 'Esta obra captura la esencia de un atardecer en un entorno natural. La atalaya, con su estructura robusta y detalles arquitectónicos, se erige como un punto focal en la composición.',
        'tecnica' => 'acuarela',
        'tema' => 'paisaje',
        'alto' => 30,
        'ancho' => 40,
        'imagen' => 'acuarela-japon.webp',
    ],
    [
        'slug' => 'mansion-en-el-bosque',
        'titulo' => 'Mansión en el bosque',
        'descripcion' => 'Esta ilustración evoca un ambiente de misterio y encanto. La mansión, con su arquitectura detallada y elementos góticos, se sitúa en un entorno forestal denso. Los árboles que la rodean añaden un aire de aislamiento y secreto, mientras que los colores oscuros y sombríos transmiten una sensación de melancolía.',
        'tecnica' => 'acuarela',
        'tema' => 'urbano',
        'alto' => 30,
        'ancho' => 40,
        'imagen' => 'acuarela-mansion.webp',
    ],
    [
        'slug' => 'transporte-de-la-ciudad',
        'titulo' => 'Transporte de la ciudad',
        'descripcion' => 'Esta obra captura la esencia del transporte urbano con un enfoque dinámico y contemporáneo. La representación de vehículos en movimiento, combinada con una paleta de colores vibrantes, sugiere energía y ritmo de vida en la ciudad.',
        'tecnica' => 'acuarela',
        'tema' => 'urbano',
        'alto' => 30,
        'ancho' => 40,
        'imagen' => 'acuarela-tren.webp',
    ],
];
