<?php

namespace App\Livewire;

use App\Models\Loan;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

#[Layout('layouts.public-layout')]
class RegistroPrestamo extends Component
{
    use WithPagination;

    public $search = '';

    public function mount()
    {
        abort_unless(
            Auth::user()->hasAnyRole(['bibliotecario', 'encargado_prestamos']),
            403
        );
    }

    public function confirmDelivery($loanId)
    {
        $loan = Loan::find($loanId);

        if (!$loan || $loan->status !== 'approved') return;

        $loan->update([
            'loan_date' => now(),
            'status'    => 'active',
        ]);

        $this->dispatch('notify', 'Préstamo #' . $loan->id . ' activado. Ejemplar entregado al usuario.');
    }

    public function render()
    {
        $approved_loans = Loan::where('status', 'approved')
            ->when($this->search, function ($q) {
                $q->whereHas('user', function ($u) {
                    $u->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('institutional_code', 'like', '%' . $this->search . '%');
                })->orWhereHas('copy.book', function ($b) {
                    $b->where('title', 'like', '%' . $this->search . '%');
                });
            })
            ->with(['user', 'copy.book'])
            ->latest()
            ->paginate(10);

        return view('livewire.registro-prestamo', [
            'approved_loans' => $approved_loans,
            'total_approved' => Loan::where('status', 'approved')->count(),
        ]);
    }
}