<?php

namespace App\Livewire;

use App\Models\Fine;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.public-layout')]
class Multas extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_status = 'pending'; // 'pending' o 'paid'

    // Registrar el pago de una multa (Caja)
    public function payFine($fineId)
    {
        $fine = Fine::with('user')->find($fineId);

        if (!$fine || $fine->status === 'paid') return;

        // 1. Marcar la multa como pagada
        $fine->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        // 2. Restar el monto de la deuda total del usuario
        $user = $fine->user;
        $user->decrement('total_debt', $fine->amount);

        // Aseguramos que la deuda no sea negativa por error de redondeo
        if ($user->total_debt < 0) {
            $user->update(['total_debt' => 0]);
        }

        $this->dispatch('notify', 'Pago registrado. La deuda del usuario ha sido actualizada.');
    }

    public function render()
    {
        $query = Fine::with(['user', 'loan.copy.book'])
            ->where('status', $this->filter_status);

        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('user', function($u) {
                    $u->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('institutional_code', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Préstamos activos y vencidos que aún no generaron multa formal
        $overdueLoansQuery = \App\Models\Loan::with(['user', 'copy.book'])
            ->where('status', 'active')
            ->where('return_date', '<', now())
            ->whereDoesntHave('fine'); // evitar duplicados

        if ($this->search) {
            $overdueLoansQuery->whereHas('user', function($u) {
                $u->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('institutional_code', 'like', '%' . $this->search . '%');
            });
        }

        // Calcular multa acumulada por cada préstamo vencido activo
        $overdueLoans = $this->filter_status === 'pending'
            ? $overdueLoansQuery->get()->map(function($loan) {
                $dueDate = \Carbon\Carbon::parse($loan->return_date)->startOfDay();
                $today = \Carbon\Carbon::now('America/Lima')->startOfDay();
                $loan->days_late = $dueDate->diffInDays($today);
                $loan->accrued_fine = $loan->days_late * 0.50;
                return $loan;
            })
            : collect();

        return view('livewire.multas', [
            'fines' => $query->latest()->paginate(10),
            'overdueLoans' => $overdueLoans,
            'total_pending' => Fine::where('status', 'pending')->sum('amount') ?? 0,
            'total_collected' => Fine::where('status', 'paid')->sum('amount') ?? 0
        ]);
    }
}