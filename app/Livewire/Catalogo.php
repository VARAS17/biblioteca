<?php

namespace App\Livewire;

use App\Models\Book;
use App\Models\Copy;
use App\Models\Reservation;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

#[Layout('layouts.public-layout')]
class Catalogo extends Component
{
    use WithPagination;

    public $search = '';
    public $selected_book = null; 

    // Función de "Apartado" para Docentes
    public function reserveBook($bookId)
    {
        // 1. Verificación de Seguridad: Solo docentes
        if (!Auth::check() || !Auth::user()->hasRole('docente')) {
            $this->dispatch('notify', 'Acceso denegado. Solo docentes pueden apartar libros.');
            return;
        }

        $user = Auth::user();

        // BLOQUEO POR DEUDA
        if ($user->real_debt >= 10) {
            $this->dispatch('notify', 'No puedes apartar libros. Tienes una deuda pendiente de S/ ' . number_format($user->real_debt, 2) . '. Regulariza tu situación en biblioteca.');
            return;
        }

        // 2. Verificar si ya tiene una reserva pendiente por este mismo título (Evitar duplicados)
        $exists = Reservation::where('user_id', $user->id)
                             ->where('book_id', $bookId)
                             ->where('status', 'pending')
                             ->exists();

        if ($exists) {
            $this->dispatch('notify', 'Ya tienes un apartado pendiente para este libro.');
            return;
        }

        // 3. Buscar el primer ejemplar físico que esté DISPONIBLE
        $copy = Copy::where('book_id', $bookId)
                    ->where('status', 'available')
                    ->first();

        if (!$copy) {
            $this->dispatch('notify', 'Lo sentimos, ya no quedan ejemplares disponibles en este momento.');
            return;
        }

        // 4. Crear el registro de Reserva (Apartado)
        Reservation::create([
            'user_id' => $user->id,
            'book_id' => $bookId,
            'reservation_date' => now(),
            'status' => 'pending'
        ]);

        // 5. MARCAR EL EJEMPLAR COMO RESERVADO
        // Esto lo quita de la disponibilidad del catálogo inmediatamente
        $copy->update(['status' => 'reserved']);

        $this->dispatch('notify', '¡Libro apartado! Tienes 48 horas para recoger el ejemplar ' . $copy->inventory_code);
    }

    public function showDetails($bookId)
    {
        $this->selected_book = Book::with(['category', 'authors', 'copies'])->find($bookId);
    }

    public function render()
    {
        $books = Book::where('title', 'like', '%' . $this->search . '%')
            ->orWhere('isbn', 'like', '%' . $this->search . '%')
            ->orWhereHas('authors', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->with(['category', 'authors', 'copies'])
            ->paginate(12);

        return view('livewire.catalogo', [
            'books' => $books
        ]);
    }
}