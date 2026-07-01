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
        Schema::table('loans', function (Blueprint $table) {
            // Nuevo flujo: pending → approved → active → returned
            $table->enum('status', ['pending', 'approved', 'active', 'returned', 'overdue', 'rejected'])
                ->default('pending')
                ->change();
            
            // Nullable hasta que encargado_catalogo asigne el ejemplar
            $table->foreignId('copy_id')->nullable()->change();
            
            // El libro que pidió el usuario (antes de asignar ejemplar físico)
            $table->foreignId('book_id')->nullable()->after('user_id')
                ->constrained()->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            //
        });
    }
};
