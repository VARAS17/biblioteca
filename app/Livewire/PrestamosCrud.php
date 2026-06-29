<?php

namespace App\Livewire;

use App\Models\Loan;
use App\Models\User;
use App\Models\Copy;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon; // Para manejar fechas

#[Layout('layouts.public-layout')]
class PrestamosCrud extends Component
{
    use WithPagination;

    public $search_user = '';
    public $search_copy = '';
    public $return_date; 
    public $selectedUser = null;
    public $selectedCopy = null;

    public function findUser()
    {
        $user = User::where('institutional_code', $this->search_user)
                    ->orWhere('dni', $this->search_user)
                    ->first();

        if (!$user) {
            $this->addError('search_user', 'Usuario no encontrado.');
            return;
        }

        $this->selectedUser = $user;

        // Sugerencia inicial basada en política (7 o 14)
        $days = $user->hasRole('docente') ? 14 : 7;
        $this->return_date = now()->addDays($days)->format('Y-m-d');
        
        $this->resetErrorBag('search_user');
    }

    public function findCopy()
    {
        $copy = Copy::where('inventory_code', $this->search_copy)->with('book')->first();
        if (!$copy || $copy->status !== 'available') {
            $this->addError('search_copy', 'Ejemplar no disponible.');
            return;
        }
        $this->selectedCopy = $copy;
    }

    public function processLoan()
    {
        if (!$this->selectedUser || !$this->selectedCopy) return;

        // --- LÓGICA DE VALIDACIÓN DE LÍMITES POR ROL ---
        
        // 1. Definimos el máximo según rol
        $maxAllowedDays = $this->selectedUser->hasRole('docente') ? 14 : 7;
        
        // 2. Calculamos cuántos días está pidiendo el bibliotecario en el input
        $requestedDate = Carbon::parse($this->return_date)->startOfDay();
        $today = now()->startOfDay();
        $requestedDays = $today->diffInDays($requestedDate);

        // 3. Validamos contra el tope del rol
        if ($requestedDays > $maxAllowedDays) {
            $roleName = $this->selectedUser->hasRole('docente') ? 'Docente' : 'Alumno';
            $this->addError('return_date', "Error: El plazo máximo para un {$roleName} es de {$maxAllowedDays} días. (Estás intentando poner {$requestedDays} días)");
            return;
        }

        // 4. Validación básica de fecha pasada
        if ($requestedDate->lt($today)) {
            $this->addError('return_date', "La fecha no puede ser anterior a hoy.");
            return;
        }
            
        // BLOQUEO POR DEUDA
        if ($this->selectedUser->real_debt >= 10) {
            $this->addError('search_user', "Usuario bloqueado: tiene una deuda pendiente de S/ " . number_format($this->selectedUser->real_debt, 2) . ". Debe regularizar su situación antes de solicitar un préstamo.");
            return;
        }

        // Si pasa las validaciones, creamos el préstamo
        Loan::create([
            'user_id' => $this->selectedUser->id,
            'copy_id' => $this->selectedCopy->id,
            'loan_date' => now(),
            'return_date' => $this->return_date,
            'status' => 'active'
        ]);

        $this->selectedCopy->update(['status' => 'loaned']);

        $this->reset(['search_user', 'search_copy', 'selectedUser', 'selectedCopy', 'return_date']);
        $this->dispatch('notify', 'Préstamo registrado con éxito.');
    }

    public function render()
    {
        return view('livewire.prestamos-crud', [
            'active_loans' => Loan::where('status', 'active')->with(['user', 'copy.book'])->latest()->paginate(10)
        ]);
    }
}