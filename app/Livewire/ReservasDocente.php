<?php

namespace App\Livewire;

use App\Models\Reservation;
use App\Models\Copy; // IMPORTANTE: Importar el modelo Copy
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.public-layout')]
class ReservasDocente extends Component
{
    use WithPagination;

    public $tab = 'pending';

    public function cancelReservation($id)
    {
        // 1. Buscar la reserva del docente
        $reservation = Reservation::where('user_id', Auth::id())
                                  ->where('id', $id)
                                  ->where('status', 'pending')
                                  ->first();

        if ($reservation) {
            // 2. BUSCAR EL EJEMPLAR QUE ESTABA APARTADO
            // Buscamos una copia de ese libro que esté en estado 'reserved'
            $copy = Copy::where('book_id', $reservation->book_id)
                        ->where('status', 'reserved')
                        ->first();

            // 3. LIBERAR EL EJEMPLAR (Volver a stock)
            if ($copy) {
                $copy->update(['status' => 'available']);
            }

            // 4. Marcar la reserva como cancelada
            $reservation->update(['status' => 'cancelled']);

            $this->dispatch('notify', 'Reserva cancelada. El libro ha vuelto al stock disponible.');
        }
    }

    public function render()
    {
        $user = Auth::user();

        // Seguridad
        if (!$user->hasRole('docente')) {
            return view('livewire.error-permisos'); 
        }

        if ($user->real_debt >= 10) {
            return view('livewire.reservas-docente', [
                'reservations' => collect(), // colección vacía
                'bloqueado' => true,
                'deuda' => $user->real_debt,
            ]);
        }

        $query = Reservation::where('user_id', $user->id)
                            ->with('book.category');

        if ($this->tab === 'pending') {
            $query->where('status', 'pending');
        } else {
            $query->whereIn('status', ['completed', 'cancelled', 'expired']);
        }

        return view('livewire.reservas-docente', [
            'reservations' => $query->latest()->paginate(10)
        ]);
    }
}