<div class="p-4 md:p-8 max-w-7xl mx-auto bg-gray-50 min-h-screen">
    
    <!-- CABECERA -->
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-black text-blue-900 tracking-tight">USUARIOS</h1>
            <p class="text-gray-500 font-medium">Panel de administración de la comunidad universitaria</p>
        </div>
        @if($view === 'list')
            <button wire:click="setView('create')" class="bg-blue-900 hover:bg-blue-800 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round"/></svg>
                Registrar Nuevo
            </button>
        @endif
    </div>

    @if($view === 'list')
        <!-- VISTA DE LISTADO -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                <div class="relative max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
                    </span>
                    <!-- Campo de búsqueda con marco más definido -->
                    <input wire:model.live="search" type="text" placeholder="Buscar por nombre o código..." 
                        class="pl-10 w-full border-gray-300 bg-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Datos de Usuario</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Identificación</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">Rol</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($users as $user)
                        <tr class="hover:bg-blue-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-mono text-blue-600 bg-blue-50 px-2 py-1 rounded inline-block border border-blue-100">{{ $user->institutional_code }}</div>
                                <div class="text-xs text-gray-400 mt-1 italic">DNI: {{ $user->dni }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-[10px] font-black rounded-full {{ $user->hasRole('alumno') ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700' }} uppercase border border-current opacity-80">
                                    {{ $user->roles->first()->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="editUser({{ $user->id }})" class="bg-gray-50 hover:bg-blue-100 text-blue-600 px-4 py-2 rounded-lg transition-colors font-bold text-sm border border-gray-200">
                                    Editar
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        </div>

    @else
        <!-- VISTA DE FORMULARIO -->
        <div class="max-w-4xl mx-auto">
            <button wire:click="setView('list')" class="mb-6 flex items-center text-blue-600 font-bold hover:text-blue-800 transition-all group">
                <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Volver al listado
            </button>

            <div class="bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/30">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $view === 'edit' ? 'Actualizar Información' : 'Nuevo Registro de Usuario' }}</h2>
                </div>

                <form wire:submit.prevent="saveUser" class="p-8 space-y-6" autocomplete="off">
                    
                    <input type="text" style="display:none" aria-hidden="true">
                    <input type="password" style="display:none" aria-hidden="true">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- ROL -->
                        <div class="md:col-span-2">
                            <label class="text-xs font-black text-gray-400 uppercase mb-3 block">Tipo de Miembro</label>
                            <div class="flex flex-wrap gap-3">
                                @foreach($roles as $role)
                                <label class="cursor-pointer group">
                                    <input type="radio" wire:model.live="selected_role" value="{{ $role->name }}" class="hidden peer">
                                    <div class="px-5 py-2 rounded-xl border-2 border-gray-200 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all font-bold text-gray-500 shadow-sm">
                                        {{ strtoupper($role->name) }}
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- CAMPOS PERSONALES CON MARCO MEJORADO -->
                        <div class="space-y-1">
                            <label class="text-sm font-bold text-gray-700 ml-1">Nombre Completo</label>
                            <input wire:model="name" type="text" class="w-full border-gray-300 bg-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all">
                            @error('name') <p class="text-red-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-bold text-gray-700 ml-1">DNI</label>
                            <input wire:model="dni" type="text" maxlength="8" class="w-full border-gray-300 bg-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all">
                            @error('dni') <p class="text-red-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-bold text-gray-700 ml-1">Correo Institucional</label>
                            <input wire:model="email" type="email" class="w-full border-gray-300 bg-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all" placeholder="usuario@unitru.edu.pe">
                            @error('email') <p class="text-red-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-bold text-gray-700 ml-1">Código Institucional</label>
                            <input wire:model="institutional_code" type="text" class="w-full border-gray-300 bg-white rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all">
                            <p class="text-[10px] text-gray-400 italic ml-1">Requerido: {{ $selected_role === 'alumno' ? '10 dígitos' : '4 dígitos' }}</p>
                            @error('institutional_code') <p class="text-red-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- CONTRASEÑA -->
                        <div class="md:col-span-2 p-5 bg-blue-50/30 rounded-2xl border border-blue-100 shadow-inner">
                            <label class="text-sm font-bold text-blue-900 ml-1">Contraseña de Acceso</label>
                            <input wire:model="password" type="password" 
                                autocomplete="new-password"
                                class="w-full mt-2 border-gray-300 bg-white rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm transition-all"
                                placeholder="{{ $view === 'edit' ? '••••••••' : 'Mínimo 6 caracteres' }}">
                            
                            @if($view === 'edit')
                                <div class="mt-3 flex items-center gap-2 text-amber-600 bg-white p-2 rounded-lg border border-amber-100 shadow-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"></path></svg>
                                    <p class="text-xs font-bold">No escriba nada si desea mantener la contraseña actual.</p>
                                </div>
                            @endif
                            @error('password') <p class="text-red-500 text-xs font-bold mt-2 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- CAMPOS ACADÉMICOS -->
                        <div class="space-y-1">
                            <label class="text-sm font-bold text-gray-700 ml-1">Facultad</label>
                            <input wire:model="faculty" type="text" class="w-full border-gray-300 bg-white rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm transition-all">
                            @error('faculty') <p class="text-red-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        @if($selected_role === 'alumno')
                            <div class="space-y-1">
                                <label class="text-sm font-bold text-gray-700 ml-1">Escuela Profesional</label>
                                <input wire:model="career" type="text" class="w-full border-gray-300 bg-white rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="text-sm font-bold text-gray-700 ml-1">Año de Ingreso</label>
                                <input wire:model="entry_year" type="text" maxlength="4" class="w-full border-gray-300 bg-white rounded-xl focus:ring-2 focus:ring-blue-500 shadow-sm transition-all">
                            </div>
                        @endif
                    </div>

                    <div class="pt-8 flex justify-end gap-4 border-t border-gray-100">
                        <button type="button" wire:click="setView('list')" class="px-6 py-3 text-gray-500 font-bold hover:text-gray-800 transition">
                            Cancelar
                        </button>
                        <button type="submit" class="bg-blue-900 text-white px-12 py-3 rounded-xl font-bold shadow-xl hover:bg-blue-800 transition-all transform active:scale-95 border-b-4 border-blue-950">
                            {{ $view === 'edit' ? 'Guardar Cambios' : 'Registrar Ahora' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>