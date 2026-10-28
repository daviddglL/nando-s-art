<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

// El modelo Shipment pasa a llamarse Encargo: la tabla se renombra sin perder datos
return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('shipments', 'encargos');
    }

    public function down(): void
    {
        Schema::rename('encargos', 'shipments');
    }
};
