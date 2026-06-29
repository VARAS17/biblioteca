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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            // Relación con categoría
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            $table->string('title');
            $table->string('isbn', 20)->unique(); // RN-13
            $table->string('editorial')->nullable();
            $table->year('published_year')->nullable();
            $table->text('description')->nullable();
            
            // Para RUP y escalabilidad, usamos SoftDeletes (borrado lógico)
            $table->softDeletes(); 
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
