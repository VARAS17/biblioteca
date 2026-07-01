<?php

namespace App\Livewire;

use App\Models\Loan;
use App\Models\Copy;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

#[Layout('layouts.public-layout')]
class AprobacionCatalogo extends Component
{
    use WithPagination;

    public $search = '';

    public function mount()
    {
        abort_unless(
            Auth::user()->hasAnyRole(['bibliotecario', 'encargado_catalogo']),
            403
        );
    }

    public function approveLoan($loanId)
    {
        $loan = Loan::with('book')->find($loanId);

        if (!$loan || $loan->status !== 'pending') return;

        $copy = Copy::where('book_id', $loan->book_id)
                    ->where('status', 'available')
                    ->first();

        if (!$copy) {
            $this->dispatch('notify', 'Sin ejemplares disponibles para: ' . $loan->book->title);
            return;
        }

        $loan->update([
            'copy_id' => $copy->id,
            'status'  => 'approved',
        ]);

        $copy->update(['status' => 'loaned']);

        $this->dispatch('notify', 'Ejemplar ' . $copy->inventory_code . ' asignado. Listo para entrega.');
    }

    public function rejectLoan($loanId)
    {
        $loan = Loan::find($loanId);

        if (!$loan || $loan->status !== 'pending') return;

        $loan->update(['status' => 'rejected']);

        $this->dispatch('notify', 'Solicitud rechazada.');
    }

    public function render()
    {
        $pending_loans = Loan::where('status', 'pending')
            ->when($this->search, function ($q) {
                $q->whereHas('user', function ($u) {
                    $u->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('institutional_code', 'like', '%' . $this->search . '%');
                })->orWhereHas('book', function ($b) {
                    $b->where('title', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['user', 'book.category'])
            // Agregamos un conteo de copias disponibles directamente en la relación
            ->withCount(['book as available_copies' => function($query) {
                $query->whereHas('copies', function($q) {
                    $q->where('status', 'available');
                });
            }])
            ->latest()
            ->paginate(10);

        return view('livewire.aprobacion-catalogo', [
            'pending_loans' => $pending_loans,
            'total_pending' => Loan::where('status', 'pending')->count(),
        ]);
    }
}