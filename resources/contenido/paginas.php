<?php

/*
|--------------------------------------------------------------------------
| Textos de «Sobre mí» y «Contacto»
|--------------------------------------------------------------------------
|
| Se cambian aquí, sin panel. Los saltos de línea dobles (\n\n) separan párrafos.
| El primer párrafo de «Sobre mí» sale también en la portada.
|
| La foto de «Sobre mí» va en public/images/. Si se deja en null sale la de siempre (nandofoto-352.webp).
| En «Contacto», los campos que se dejen en null no salen en la web.
|
| Los mensajes del formulario de contacto llegan a CONTACTO_EMAIL (en el .env del servidor)
| o, si no está puesto, al email de aquí abajo.
|
*/

return [
    'sobre-mi' => [
        'titulo' => 'Sobre mí',
        'contenido' => "Soy Fernando Delgado, un apasionado del arte y el diseño con años de experiencia en distintas técnicas y estilos.\n\nDesde acuarelas suaves hasta trazos marcados con rotulador, cada obra cuenta una historia distinta. En este sitio comparto mi trabajo, mi evolución y mis ideas.",
        'foto' => null,
    ],

    'contacto' => [
        'titulo' => 'Contacto',
        'contenido' => "¿Quieres una obra, un encargo personalizado o simplemente saludar? Escríbeme y te respondo lo antes posible.\n\nSi ya tienes una idea concreta, cuéntame en el mensaje las medidas, la técnica y lo que te imaginas.",
        'email' => 'info@fernandodelgadoart.com',
        'telefono' => null,
        'instagram' => 'https://www.instagram.com/nandomtgalters/',
        'ubicacion' => null,
    ],
];
