<div class="p-6">
    {{-- HEADER --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-1">Encargado de Catálogo</p>
            <h2 class="text-2xl font-black text-stone-900 tracking-tight">Solicitudes Pendientes</h2>
        </div>
        <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-5 py-3">
            <div class="h-2.5 w-2.5 rounded-full bg-amber-400 animate-pulse"></div>
            <span class="text-sm font-black text-amber-800">{{ $total_pending }} por revisar</span>
        </div>
    </div>

    {{-- BUSCADOR --}}
    <div class="mb-6 relative w-full md:w-80">
        <input wire:model.live="search" type="text" placeholder="Usuario, código o título..."
            class="w-full pl-10 pr-4 py-2.5 bg-white border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-stone-400 focus:outline-none shadow-sm">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        <table class="min-w-full divide-y divide-stone-100">
            <thead class="bg-stone-50">
                <tr>
                    <th class="px-6 py-4 text-left text-[10px] font-black text-stone-400 uppercase tracking-widest">Solicitante</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black text-stone-400 uppercase tracking-widest">Libro Solicitado</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black text-stone-400 uppercase tracking-widest">Fecha Límite</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black text-stone-400 uppercase tracking-widest">Disponibilidad</th>
                    <th class="px-6 py-4 text-right text-[10px] font-black text-stone-400 uppercase tracking-widest">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                @forelse($pending_loans as $loan)
                <tr class="hover:bg-stone-50 transition" wire:key="loan-{{ $loan->id }}">
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-stone-800">{{ $loan->user->name ?? 'Usuario no encontrado' }}</p>
                        <p class="text-[10px] font-mono text-blue-700 mt-0.5">{{ $loan->user->institutional_code ?? 'S/C' }}</p>
                        
                        @if($loan->user)
                            <span class="inline-block mt-1 text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full
                                {{ $loan->user->hasRole('docente') ? 'bg-blue-100 text-blue-700' : 'bg-stone-100 text-stone-500' }}">
                                {{ $loan->user->hasRole('docente') ? 'Docente' : 'Alumno' }}
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4">
                        {{-- El nombre del libro corregido --}}
                        <p class="text-sm font-bold text-stone-800 leading-tight">
                            {{ $loan->book->title ?? 'Error: Libro no asignado' }}
                        </p>
                        <p class="text-[10px] text-stone-400 mt-0.5">
                            {{ $loan->book->category->name ?? 'Sin categoría' }}
                        </p>
                    </td>

                    <td class="px-6 py-4">
                        <span class="text-xs font-bold text-stone-600">
                            {{ optional($loan->return_date)->format('d/m/Y') ?? 'Sin fecha' }}
                        </span>
                        <p class="text-[10px] text-stone-400 mt-0.5">
                            {{ ($loan->user && $loan->user->hasRole('docente')) ? '14 días' : '7 días' }}
                        </p>
                    </td>

                    <td class="px-6 py-4">
                        {{-- STOCK: Usamos el conteo que viene del controlador --}}
                        @if($loan->available_copies > 0)
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full bg-green-500"></div>
                                <span class="text-xs font-bold text-green-700">
                                    {{ $loan->available_copies }} {{ Str::plural('disponible', $loan->available_copies) }}
                                </span>
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <div class="h-2 w-2 rounded-full bg-red-500"></div>
                                <span class="text-xs font-bold text-red-600">Sin stock físico</span>
                            </div>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($loan->available_copies > 0)
                                <button wire:click="approveLoan({{ $loan->id }})"
                                    wire:confirm="¿Confirmas asignar un ejemplar a {{ $loan->user->name }}?"
                                    class="bg-stone-800 hover:bg-black text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition">
                                    Asignar
                                </button>
                            @else
                                <span class="text-[9px] font-black text-stone-300 uppercase tracking-widest bg-stone-50 px-3 py-2 rounded-xl border border-stone-100">
                                    No disponible
                                </span>
                            @endif

                            <button wire:click="rejectLoan({{ $loan->id }})"
                                wire:confirm="¿Rechazar esta solicitud?"
                                class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition">
                                Rechazar
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="h-16 w-16 bg-stone-50 rounded-full flex items-center justify-center">
                                <svg class="h-8 w-8 text-stone-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-stone-400 uppercase tracking-widest">No hay solicitudes pendientes</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($pending_loans->hasPages())
            <div class="p-4 border-t border-stone-100 bg-stone-50">
                {{ $pending_loans->links() }}
            </div>
        @endif
    </div>
</div>