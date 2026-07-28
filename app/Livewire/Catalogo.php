<?php

namespace App\Livewire;

use App\Models\{Book, Category, Author, Copy, Loan};
use Livewire\{Component, WithPagination};
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.public-layout')]
class Catalogo extends Component
{
    use WithPagination;

    // Propiedades de búsqueda y estado
    public $search = '';
    public $selected_book = null; // Guardaremos el ID o el Array del libro, no el objeto vivo para evitar errores de hidratación

    // Resetear paginación cuando se busca
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Muestra el modal cargando el libro con sus relaciones
     */
    public function showDetails($bookId)
    {
        // Cargamos el libro y lo convertimos a array para que Livewire lo maneje mejor en el modal
        $this->selected_book = Book::with(['category', 'authors'])
            ->findOrFail($bookId)
            ->toArray();
    }

    /**
     * CIERRA EL MODAL (Solución al error de cierre)
     */
    public function closeDetails()
    {
        $this->selected_book = null;
        $this->resetErrorBag();
    }

    /**
     * Lógica de Reserva/Apartado para Docentes
     */
    public function reserveBook($bookId)
    {
        // 1. Verificación de Autenticación y Rol
        if (!Auth::check() || !Auth::user()->hasRole('docente')) {
            $this->dispatch('notify', 'Solo docentes pueden realizar reservas desde el catálogo.');
            return;
        }

        $user = Auth::user();

        // 2. Verificación de Deuda (Heurística: Prevención de errores)
        if ($user->real_debt >= 10) {
            $this->dispatch('notify', 'Bloqueado: tiene una deuda pendiente de S/ ' . number_format($user->real_debt, 2));
            return;
        }

        // 3. Límite de Préstamos Activos
        $activeCount = Loan::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'active'])
            ->count();

        if ($activeCount >= 5) {
            $this->dispatch('notify', 'Límite alcanzado: máximo 5 préstamos/reservas permitidas.');
            return;
        }

        // 4. Evitar duplicados para el mismo libro
        $exists = Loan::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->whereIn('status', ['pending', 'approved', 'active'])
            ->exists();

        if ($exists) {
            $this->dispatch('notify', 'Ya tienes una solicitud activa para este libro.');
            return;
        }

        // 5. Creación de la solicitud
        Loan::create([
            'user_id'     => $user->id,
            'book_id'     => $bookId,
            'copy_id'     => null, // Se asigna cuando el bibliotecario aprueba
            'loan_date'   => now(),
            'return_date' => now()->addDays(14),
            'status'      => 'pending',
        ]);

        $this->closeDetails(); // Cerrar modal tras éxito
        $this->dispatch('notify', '¡Reserva registrada con éxito! Espere la aprobación.');
    }

    public function render()
    {
        // Búsqueda avanzada incluyendo autores y categorías
        $books = Book::query()
            ->where(function($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('isbn', 'like', '%' . $this->search . '%')
                      ->orWhereHas('authors', function($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('category', function($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->with(['category', 'authors']) // Cargamos relaciones necesarias
            ->latest()
            ->paginate(12);

        return view('livewire.catalogo', [
            'books' => $books
        ]);
    }
}