<div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
    
    <!-- COLUMNA IZQUIERDA: Formulario de Préstamo -->
    <div class="md:col-span-1 space-y-6">
        <div class="bg-white p-6 rounded-xl shadow-lg border-t-4 border-blue-900">
            <h3 class="text-lg font-bold text-blue-900 mb-6 uppercase tracking-tighter">Nuevo Préstamo</h3>
            
            <!-- 1. Buscador de Usuario -->
            <div class="mb-6">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">1. Identificar Usuario (Código/DNI)</label>
                <div class="flex mt-1">
                    <input wire:model="search_user" type="text" class="w-full border-gray-300 rounded-l-lg text-sm" placeholder="Ej: 1052700622" wire:keydown.enter="findUser">
                    <button wire:click="findUser" class="bg-blue-900 text-white px-4 rounded-r-lg hover:bg-blue-800 transition">🔍</button>
                </div>
                @error('search_user') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror

                @if($selectedUser)
                    <div class="mt-3 p-3 bg-blue-50 rounded-xl border border-blue-100 flex items-center">
                        <div class="h-8 w-8 rounded-full bg-blue-900 text-white flex items-center justify-center text-xs font-bold mr-3">
                            {{ substr($selectedUser->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-blue-900 leading-tight">{{ $selectedUser->name }}</p>
                            <p class="text-[10px] text-blue-700 uppercase font-black tracking-tighter">
                                {{ $selectedUser->roles->first()->name }} | {{ $selectedUser->institutional_code }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- 2. Buscador de Ejemplar -->
            <div class="mb-6 {{ !$selectedUser ? 'opacity-30 pointer-events-none' : '' }}">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">2. Escanear Libro (Cód. Inventario)</label>
                <div class="flex mt-1">
                    <input wire:model="search_copy" type="text" class="w-full border-gray-300 rounded-l-lg text-sm font-mono" placeholder="Ej: INF-001" wire:keydown.enter="findCopy">
                    <button wire:click="findCopy" class="bg-blue-900 text-white px-4 rounded-r-lg hover:bg-blue-800 transition">🔍</button>
                </div>
                @error('search_copy') <span class="text-red-500 text-[10px] font-bold mt-1 block uppercase">{{ $message }}</span> @enderror

                @if($selectedCopy)
                    <div class="mt-3 p-3 bg-green-50 rounded-xl border border-green-100">
                        <p class="text-[10px] font-black text-green-700 uppercase tracking-widest mb-1">Libro Detectado</p>
                        <p class="text-sm font-bold text-green-900 leading-tight">{{ $selectedCopy->book->title }}</p>
                        <p class="text-[10px] text-green-600 mt-1 italic">Ubicación: {{ $selectedCopy->location ?? 'Sala de Informática' }}</p>
                    </div>
                @endif
            </div>

            <!-- 3. Fecha de Devolución (Lógica de Límites) -->
            <div class="mb-8 {{ !$selectedCopy ? 'opacity-30 pointer-events-none' : '' }}">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    3. Fecha de Entrega (Máx: {{ $selectedUser?->hasRole('docente') ? '14' : '7' }} días)
                </label>
                <input wire:model="return_date" type="date" 
                    class="mt-1 w-full border-blue-200 bg-blue-50 rounded-xl text-sm font-black text-blue-900 focus:ring-blue-900 focus:border-blue-900 transition">
                
                @error('return_date') 
                    <div class="mt-2 p-2 bg-red-100 text-red-700 text-[10px] font-black rounded-lg border border-red-200 uppercase flex items-center">
                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button wire:click="processLoan" 
                {{ (!$selectedUser || !$selectedCopy) ? 'disabled' : '' }}
                class="w-full py-4 rounded-2xl font-black text-sm text-white shadow-xl transition uppercase tracking-widest {{ (!$selectedUser || !$selectedCopy) ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 shadow-green-100' }}">
                Confirmar Préstamo
            </button>
        </div>
    </div>

    <!-- COLUMNA DERECHA: Préstamos Activos -->
    <div class="md:col-span-2">
        <div class="bg-white rounded-[2rem] shadow-lg overflow-hidden border border-gray-100">
            <div class="p-6 bg-gray-50 border-b">
                <h3 class="font-black text-blue-900 uppercase text-xs tracking-widest">Préstamos en curso (Sala de Informática)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Libro / Ejemplar</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Usuario</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Vencimiento</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-xs">
                        @foreach($active_loans as $loan)
                        <tr class="hover:bg-blue-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $loan->copy->book->title }}</div>
                                <div class="text-[10px] text-blue-600 font-mono mt-0.5">{{ $loan->copy->inventory_code }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-700">{{ $loan->user->name }}</div>
                                <div class="text-[10px] text-gray-400 uppercase">{{ $loan->user->institutional_code }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-black {{ $loan->return_date < now() ? 'text-red-600' : 'text-gray-700' }}">
                                    {{ \Carbon\Carbon::parse($loan->return_date)->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($loan->return_date < now())
                                    <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-[9px] font-black uppercase tracking-tighter border border-red-200">Atrasado</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 text-[9px] font-black uppercase tracking-tighter border border-blue-200">En curso</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-white border-t">{{ $active_loans->links() }}</div>
        </div>
    </div>
</div>