<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Ej: "Base de Datos"
            $table->string('slug')->unique(); // Ej: "base-de-datos"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
