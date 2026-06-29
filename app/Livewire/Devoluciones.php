<?php

namespace App\Livewire;

use App\Models\Loan;
use App\Models\Copy;
use App\Models\Fine;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.public-layout')]
class Devoluciones extends Component
{
    use WithPagination;

    public $search_active = ''; 
    public $selectedLoanId = null;
    public $selectedLoan = null;
    public $days_late = 0;
    public $fine_amount = 0;

public function selectLoan($loanId)
{
    $this->selectedLoanId = $loanId;
    $this->selectedLoan = Loan::with(['user', 'copy.book'])->find($loanId);
    
    if ($this->selectedLoan) {
        $this->calculateFine();
    }
}

public function calculateFine()
{
    $dueDate = Carbon::parse($this->selectedLoan->return_date)
                     ->timezone('America/Lima')
                     ->startOfDay();
    
    $today = Carbon::now('America/Lima')->startOfDay();

    if ($today->gt($dueDate)) {
        // ✅ dueDate->diffInDays($today), no al revés
        $this->days_late = $dueDate->diffInDays($today);
        $this->fine_amount = $this->days_late * 0.50;
    } else {
        $this->days_late = 0;
        $this->fine_amount = 0;
    }
}

public function processReturn()
{
    // Recuperar el modelo FRESCO desde la BD, no desde el estado de Livewire
    $loan = Loan::with(['user', 'copy.book'])->find($this->selectedLoanId);
    
    if (!$loan) return;

    $loan->update([
        'actual_return_date' => now(),
        'status' => 'returned'
    ]);

    $loan->copy->update(['status' => 'available']);

    if ($this->fine_amount > 0) {
        Fine::create([
            'loan_id' => $loan->id,
            'user_id' => $loan->user_id,
            'amount' => $this->fine_amount,
            'days_late' => $this->days_late,
            'status' => 'pending'
        ]);

        User::find($loan->user_id)->increment('total_debt', $this->fine_amount);
    }

    $this->reset(['selectedLoan', 'selectedLoanId', 'days_late', 'fine_amount', 'search_active']);
    $this->dispatch('notify', '¡Devolución exitosa! El stock y la deuda han sido actualizados.');
}

    public function render()
    {
        $active_loans = Loan::where('status', 'active')
            ->where(function($query) {
                $query->whereHas('user', function($q) {
                    $q->where('name', 'like', '%' . $this->search_active . '%')
                      ->orWhere('institutional_code', 'like', '%' . $this->search_active . '%');
                })
                ->orWhereHas('copy.book', function($q) {
                    $q->where('title', 'like', '%' . $this->search_active . '%');
                })
                ->orWhereHas('copy', function($q) {
                    $q->where('inventory_code', 'like', '%' . $this->search_active . '%');
                });
            })
            ->with(['user', 'copy.book'])
            ->latest()
            ->paginate(10);

        return view('livewire.devoluciones', [
            'active_loans' => $active_loans,
            'recent_returns' => Loan::where('status', 'returned')
                                    ->with(['user', 'copy.book'])
                                    ->latest('actual_return_date')
                                    ->take(5)
                                    ->get()
        ]);
    }
}