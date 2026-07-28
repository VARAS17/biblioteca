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
        $admin1 = Role::create(['name' => 'encargado_catalogo']);
        $admin2 = Role::create(['name' => 'encargado_usuarios']);
        $admin3 = Role::create(['name' => 'encargado_prestamos']);
        $docente = Role::create(['name' => 'docente']);
        $alumno = Role::create(['name' => 'alumno']);


        // =====================================================
        // 2. Usuario Bibliotecario (Administrador)
        // =====================================================

        $bibliotecario = User::create([
            'name' => 'Admin',
            'email' => 'admin@unitru.edu.pe',
            'password' => bcrypt('theadminuser2026'),
            'institutional_code' => '0001',
            'is_active' => true,
            'max_books' => 10,
            'loan_days' => 30,
        ]);

        $bibliotecario1 = User::create([
            'name' => 'Admin1',
            'email' => 'admin1@unitru.edu.pe',
            'password' => bcrypt('theadminuser2026'),
            'institutional_code' => '0002',
            'is_active' => true,
            'max_books' => 10,
            'loan_days' => 30,
        ]);

        $bibliotecario2 = User::create([
            'name' => 'Admin2',
            'email' => 'admin2@unitru.edu.pe',
            'password' => bcrypt('theadminuser2026'),
            'institutional_code' => '0003',
            'is_active' => true,
            'max_books' => 10,
            'loan_days' => 30,
        ]);

        $bibliotecario3 = User::create([
            'name' => 'Admin3',
            'email' => 'admin3@unitru.edu.pe',
            'password' => bcrypt('theadminuser2026'),
            'institutional_code' => '0004',
            'is_active' => true,
            'max_books' => 10,
            'loan_days' => 30,
        ]);

        $bibliotecario->assignRole($admin);
        $bibliotecario1->assignRole($admin1);
        $bibliotecario2->assignRole($admin2);
        $bibliotecario3->assignRole($admin3);



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