<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Datos de identidad UNT
            $table->string('dni', 8)->unique()->nullable()->after('email');
            $table->string('institutional_code')->unique()->after('dni');
            $table->string('faculty')->nullable()->after('institutional_code');
            
            // Datos específicos de Alumno
            $table->string('career')->nullable()->after('faculty');
            $table->year('entry_year')->nullable()->after('career');

            // Límites de Biblioteca (RN-02 a RN-05)
            // Usamos tinyInteger para ahorrar espacio en BD (números pequeños)
            $table->unsignedTinyInteger('max_books')->default(3)->after('entry_year');
            $table->unsignedTinyInteger('loan_days')->default(7)->after('max_books');

            // Estado y Deuda (RN-09 y RN-12)
            $table->decimal('total_debt', 8, 2)->default(0.00)->after('loan_days');
            $table->boolean('is_active')->default(true)->after('total_debt');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'dni', 'institutional_code', 'faculty', 'career', 
                'entry_year', 'max_books', 'loan_days', 'total_debt', 'is_active'
            ]);
        });
    }
};
