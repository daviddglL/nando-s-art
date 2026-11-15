<?php

use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Los tests de Feature arrancan la aplicación de Laravel (rutas, vistas, emails...).
| La web no tiene base de datos, así que no hace falta RefreshDatabase.
|
*/

pest()->extend(TestCase::class)->in('Feature');
