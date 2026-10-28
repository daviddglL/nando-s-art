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

];
