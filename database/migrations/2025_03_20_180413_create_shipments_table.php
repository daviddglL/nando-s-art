<?php

use App\Enums\ShipmentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->text('description')->nullable();
            $table->decimal('height', 8, 2);
            $table->decimal('width', 8, 2);
            $table->string('style');
            $table->string('category');
            $table->string('imagen')->nullable();
            $table->enum('status', ShipmentStatus::values())->default(ShipmentStatus::Pending->value);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
