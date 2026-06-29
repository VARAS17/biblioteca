<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\LibrosCrud;
use App\Livewire\UsuariosCrud;
use App\Livewire\PrestamosCrud;
use App\Livewire\Devoluciones;
use App\Livewire\Multas;
use App\Livewire\Catalogo;
use App\Livewire\PrestamosUser;
use App\Livewire\ReservasDocente;
use App\Livewire\ReservasAdmin;


Route::view('/', 'home')->name('home');
Route::get('/catalogo', Catalogo::class)->name('catalogo');

Route::middleware(['auth', 'verified'])->group(function () {
    // Cambia la vista del dashboard para que simplemente redirija al inicio
    Route::get('/dashboard', function () {
        return redirect('/');
    })->name('dashboard');
    Route::get('/mis-prestamos', PrestamosUser::class)->name('mis.prestamos');
    
});
Route::middleware(['role:bibliotecario'])->group(function () {
        
        Route::get('/admin/libros', LibrosCrud::class)->name('admin.libros');
        Route::get('/admin/usuarios',UsuariosCrud::class)->name('admin.usuarios');
        Route::get('/admin/prestamos', PrestamosCrud::class)->name('admin.prestamos');
        Route::get('/admin/devoluciones', Devoluciones::class)->name('admin.devoluciones');
        Route::get('/admin/multas', Multas::class)->name('admin.multas');
        Route::get('/admin/reservas', ReservasAdmin::class)->name('admin.reservas');
        
    });

Route::middleware(['role:docente'])->group(function () {
    Route::get('/reservas-docente', ReservasDocente::class)->name('reservas.docente');
});

require __DIR__.'/settings.php';
