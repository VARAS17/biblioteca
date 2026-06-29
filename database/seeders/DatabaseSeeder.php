<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crear Roles de Spatie
        $admin = Role::create(['name' => 'bibliotecario']);
        $docente = Role::create(['name' => 'docente']);
        $alumno = Role::create(['name' => 'alumno']);


        // =====================================================
        // 2. Usuario Bibliotecario (Administrador)
        // =====================================================
        $bibliotecario = User::create([
            'name' => 'Administrador Biblioteca',
            'email' => 'admin@unitru.edu.pe',
            'password' => bcrypt('theadminuser2026'),
            'institutional_code' => '0001',
            'is_active' => true,
            'max_books' => 10,
            'loan_days' => 30,
        ]);

        $bibliotecario->assignRole($admin);



        // =====================================================
        // 3. Usuario Docente
        // =====================================================
        $usuarioDocente = User::create([
            'name' => 'Docente Prueba',
            'email' => 'docente@unitru.edu.pe',
            'password' => bcrypt('docente2026'),
            'institutional_code' => '1001',
            'is_active' => true,
            'max_books' => 5,
            'loan_days' => 15,
        ]);

        $usuarioDocente->assignRole($docente);



        // =====================================================
        // 4. Usuario Alumno
        // =====================================================
        $usuarioAlumno = User::create([
            'name' => 'Alumno Prueba',
            'email' => 'alumno@unitru.edu.pe',
            'password' => bcrypt('alumno2026'),
            'institutional_code' => '2001',
            'is_active' => true,
            'max_books' => 3,
            'loan_days' => 7,
        ]);

        $usuarioAlumno->assignRole($alumno);
    }
}