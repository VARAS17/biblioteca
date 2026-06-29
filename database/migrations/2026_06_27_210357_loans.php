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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('copy_id')->constrained()->onDelete('cascade');
            $table->dateTime('loan_date');
            $table->dateTime('return_date'); // Fecha límite esperada
            $table->dateTime('actual_return_date')->nullable(); // Para cuando lo devuelva
            $table->enum('status', ['active', 'returned', 'overdue'])->default('active');
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
