<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.public-layout')]
class UsuariosCrud extends Component
{
    use WithPagination;

    // Estado de la navegación (Principio 1: Visibilidad)
    public $view = 'list'; 

    // Propiedades del modelo
    public $user_id, $name, $email, $dni, $institutional_code, $faculty, $career, $entry_year, $password;
    public $selected_role = 'alumno'; 
    public $search = '';

    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'email' => ['required', 'email', 'unique:users,email,' . $this->user_id, 'regex:/^[a-z0-9.]+@unitru\.edu\.pe$/i'],
            'dni' => 'required|digits:8|unique:users,dni,' . $this->user_id,
            'selected_role' => 'required|exists:roles,name',
            'institutional_code' => $this->selected_role === 'alumno' ? 'required|digits:10|unique:users,institutional_code,' . $this->user_id : 'required|digits:4|unique:users,institutional_code,' . $this->user_id,
            'faculty' => 'required',
            'career' => $this->selected_role === 'alumno' ? 'required' : 'nullable',
            'entry_year' => $this->selected_role === 'alumno' ? 'required|digits:4' : 'nullable',
            'password' => $this->view === 'create' ? 'required|min:6' : 'nullable|min:6',
        ];
    }

    // Principio 3: Libertad del usuario - Cambiar entre vistas
    public function setView($view)
    {
        $this->view = $view;
        if ($view === 'create') {
            $this->resetFields(); // Garantiza campos vacíos (Tu requerimiento)
        }
        $this->resetValidation();
    }

    private function resetFields()
    {
        $this->reset(['user_id', 'name', 'email', 'dni', 'institutional_code', 'faculty', 'career', 'entry_year', 'password']);
        $this->selected_role = 'alumno';
    }

    public function saveUser()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'dni' => $this->dni,
            'institutional_code' => $this->institutional_code,
            'faculty' => $this->faculty,
            'career' => $this->career,
            'entry_year' => $this->entry_year,
            'max_books' => $this->selected_role === 'alumno' ? 3 : 5,
            'loan_days' => $this->selected_role === 'alumno' ? 7 : 15,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user = User::updateOrCreate(['id' => $this->user_id], $data);
        $user->syncRoles([$this->selected_role]);

        $this->dispatch('notify', 'Operación realizada con éxito.');
        $this->setView('list');
    }

    public function editUser(User $user)
    {
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->dni = $user->dni;
        $this->institutional_code = $user->institutional_code;
        $this->faculty = $user->faculty;
        $this->career = $user->career;
        $this->entry_year = $user->entry_year;
        $this->selected_role = $user->roles->first()->name ?? 'alumno';
        $this->password = ''; // Vacío para no mostrar la actual (Seguridad)
        
        $this->view = 'edit';
    }

    public function render()
    {
        return view('livewire.usuarios-crud', [
            'users' => User::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('institutional_code', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),
            'roles' => Role::whereIn('name', ['alumno', 'docente', 'bibliotecario'])->get()
        ]);
    }
}