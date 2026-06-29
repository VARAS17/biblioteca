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
        Schema::create('copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            
            // Código de inventario único (el que pegas en el lomo del libro)
            $table->string('inventory_code')->unique(); 
            
            // Estados según el modelo del dominio (pág. 21 del informe)
            $table->enum('status', ['available', 'loaned', 'reserved', 'maintenance'])
                ->default('available');
                
            $table->string('location')->nullable(); // Ej: "Estante A-1"
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
