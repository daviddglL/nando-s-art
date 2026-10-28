<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Vacía la papelera de obras: borra las que llevan más de 30 días (Product::prunable) con su imagen.
// Necesita el cron de Laravel en el servidor (ver la guía de despliegue del README).
Schedule::command('model:prune')->daily();
