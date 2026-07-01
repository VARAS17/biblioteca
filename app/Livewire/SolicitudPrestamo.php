<?php

namespace App\Livewire;

use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

#[Layout('layouts.public-layout')]
class SolicitudPrestamo extends Component
{
    use WithPagination;

    public $search_user = '';
    public $search_book = '';
    public $selectedUser = null;
    public $selectedBook = null;
    public $selectedUserId = null;
    public $selectedBookId = null;

    public function mount()
    {
        abort_unless(
            Auth::user()->hasAnyRole(['bibliotecario', 'encargado_usuarios']),
            403
        );
    }

    public function findUser()
    {
        $user = User::where('institutional_code', $this->search_user)
                    ->orWhere('dni', $this->search_user)
                    ->first();

        if (!$user) {
            $this->addError('search_user', 'Usuario no encontrado.');
            return;
        }

        // Bloqueo por deuda real (incluye préstamos vencidos no devueltos)
        if ($user->real_debt >= 10) {
            $this->addError('search_user', 'Usuario bloqueado: deuda pendiente de S/ ' . number_format($user->real_debt, 2) . '.');
            return;
        }

        // Límite de préstamos activos por rol
        $maxLoans = $user->hasRole('docente') ? 5 : 3;
        $activeCount = Loan::where('user_id', $user->id)
                           ->whereIn('status', ['pending', 'approved', 'active'])
                           ->count();

        if ($activeCount >= $maxLoans) {
            $this->addError('search_user', "Límite alcanzado: {$activeCount}/{$maxLoans} préstamos en curso.");
            return;
        }

        $this->selectedUser = $user;
        $this->selectedUserId = $user->id;
        $this->resetErrorBag('search_user');
    }

    public function findBook()
    {
        $book = Book::where('isbn', $this->search_book)
                    ->orWhere('title', 'like', '%' . $this->search_book . '%')
                    ->with('category')
                    ->first();

        if (!$book) {
            $this->addError('search_book', 'Libro no encontrado.');
            return;
        }

        $this->selectedBook = $book;
        $this->selectedBookId = $book->id;
        $this->resetErrorBag('search_book');
    }

    public function processRequest()
    {
        if (!$this->selectedUserId || !$this->selectedBookId) return;

        // Re-fetch frescos para evitar problemas de serialización Livewire
        $user = User::find($this->selectedUserId);
        $book = Book::find($this->selectedBookId);

        Loan::create([
            'user_id'     => $user->id,
            'book_id'     => $book->id,
            'copy_id'     => null,
            'loan_date'   => now(),
            'return_date' => now()->addDays($user->hasRole('docente') ? 14 : 7),
            'status'      => 'pending',
        ]);

        $this->reset(['search_user', 'search_book', 'selectedUser', 'selectedBook', 'selectedUserId', 'selectedBookId']);
        $this->dispatch('notify', 'Solicitud registrada. El catálogo verificará disponibilidad.');
    }

    public function render()
    {
        return view('livewire.solicitud-prestamo', [
            'recent_requests' => Loan::where('status', 'pending')
                                     ->with(['user', 'book'])
                                     ->latest()
                                     ->take(5)
                                     ->get(),
        ]);
    }
}