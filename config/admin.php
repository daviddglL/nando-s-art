<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Administrador inicial
    |--------------------------------------------------------------------------
    |
    | Datos del usuario administrador que crea UserSeeder. La contraseña se
    | lee siempre del entorno (.env) y nunca debe escribirse en el código.
    |
    */

    'name' => env('ADMIN_NAME', 'nando'),
    'email' => env('ADMIN_EMAIL', 'info@fernandodelgadoart.com'),
    'password' => env('ADMIN_PASSWORD'),

    // Usuario de pruebas admin@example.com / admin. Solo se crea si se pide expresamente
    // y nunca en producción: antes bastaba con olvidar APP_ENV=local en el servidor.
    'pruebas' => (bool) env('SEED_ADMIN_PRUEBAS', false),

    // Adónde llegan los mensajes del formulario de contacto (si no, al email de la página Contacto o al del admin)
    'email_contacto' => env('CONTACTO_EMAIL'),

];
