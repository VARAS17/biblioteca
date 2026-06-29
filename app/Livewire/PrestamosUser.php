<?php

namespace App\Livewire;

use App\Models\Loan;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.public-layout')]
class PrestamosUser extends Component
{
    use WithPagination;

    public $tab = 'activos'; // 'activos' o 'historial'

    public function render()
    {
        $user = Auth::user();
        
        $query = Loan::where('user_id', $user->id)
                     ->with(['copy.book', 'fine']);

        if ($this->tab === 'activos') {
            $query->where('status', 'active');
        } else {
            $query->where('status', 'returned');
        }

        return view('livewire.prestamos-user', [
            'loans' => $query->latest()->paginate(8),
            'active_count' => Loan::where('user_id', $user->id)->where('status', 'active')->count(),
            'real_debt' => $user->real_debt
        ]);
    }
}