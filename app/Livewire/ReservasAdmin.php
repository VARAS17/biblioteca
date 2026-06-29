<?php

namespace App\Livewire;

use App\Models\Reservation;
use App\Models\Loan;
use App\Models\Copy;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.public-layout')]
class ReservasAdmin extends Component
{
    use WithPagination;

    public $search = '';

    // Función para cuando el docente llega a recoger el libro
    public function fulfillReservation($reservationId)
    {
        $res = Reservation::with(['user', 'book'])->find($reservationId);

        if (!$res) return;

        // 1. Buscar el ejemplar que se apartó para este libro
        // (Buscamos uno que esté en estado 'reserved' para este libro)
        $copy = Copy::where('book_id', $res->book_id)
                    ->where('status', 'reserved')
                    ->first();

        if (!$copy) {
            $this->dispatch('notify', 'Error: No se encontró el ejemplar apartado físicamente.');
            return;
        }

        // 2. Crear el Préstamo formal (RN-05: 15 días para docentes)
        Loan::create([
            'user_id' => $res->user_id,
            'copy_id' => $copy->id,
            'loan_date' => now(),
            'return_date' => now()->addDays($res->user->loan_days),
            'status' => 'active'
        ]);

        // 3. Cambiar estado del ejemplar a 'loaned'
        $copy->update(['status' => 'loaned']);

        // 4. Marcar reserva como completada
        $res->update(['status' => 'completed']);

        $this->dispatch('notify', '¡Entrega procesada! El apartado ahora es un préstamo activo.');
    }

    public function render()
    {
        return view('livewire.reservas-admin', [
            'reservations' => Reservation::where('status', 'pending')
                ->whereHas('user', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('institutional_code', 'like', '%' . $this->search . '%');
                })
                ->with(['user', 'book'])
                ->latest()
                ->paginate(10)
        ]);
    }
}