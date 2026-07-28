<div class="p-6">

    {{-- HEADER --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-1">Encargado de Préstamos</p>
            <h2 class="text-2xl font-black text-stone-900 tracking-tight">Entrega de Ejemplares</h2>
        </div>
        <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-2xl px-5 py-3">
            <div class="h-2.5 w-2.5 rounded-full bg-green-500 animate-pulse"></div>
            <span class="text-sm font-black text-green-800">{{ $total_approved }} listos para entregar</span>
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
                    <th class="px-6 py-4 text-left text-[10px] font-black text-stone-400 uppercase tracking-widest">Ejemplar Asignado</th>
                    <th class="px-6 py-4 text-left text-[10px] font-black text-stone-400 uppercase tracking-widest">Devolver antes de</th>
                    <th class="px-6 py-4 text-right text-[10px] font-black text-stone-400 uppercase tracking-widest">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                @forelse($approved_loans as $loan)
                <tr class="hover:bg-stone-50 transition">
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-stone-800">{{ $loan->user->name }}</p>
                        <p class="text-[10px] font-mono text-blue-700 mt-0.5">{{ $loan->user->institutional_code }}</p>
                        <span class="inline-block mt-1 text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full
                            {{ $loan->user->hasRole('docente') ? 'bg-blue-100 text-blue-700' : 'bg-stone-100 text-stone-500' }}">
                            {{ $loan->user->hasRole('docente') ? 'Docente' : 'Alumno' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-stone-800 leading-tight">{{ $loan->copy->book->title }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-mono bg-stone-100 text-stone-600 px-2 py-0.5 rounded-lg">
                                {{ $loan->copy->inventory_code }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-bold text-stone-700">
                            {{ $loan->return_date->format('d/m/Y') }}
                        </span>
                        <p class="text-[10px] text-stone-400 mt-0.5">
                            {{ $loan->user->hasRole('docente') ? '14 días' : '7 días' }}
                        </p>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button wire:click="confirmDelivery({{ $loan->id }})"
                            wire:confirm="¿Confirmas que estás entregando físicamente el ejemplar {{ $loan->copy->inventory_code }} a {{ $loan->user->name }}?"
                            class="bg-stone-800 hover:bg-black text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition">
                            Entregar
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="h-16 w-16 bg-stone-50 rounded-full flex items-center justify-center">
                                <svg class="h-8 w-8 text-stone-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M5 12h14M12 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-stone-400 uppercase tracking-widest">No hay ejemplares listos para entregar</p>
                            <p class="text-[10px] text-stone-300">Los aprueba el Encargado de Catálogo</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($approved_loans->hasPages())
            <div class="p-4 border-t border-stone-100 bg-stone-50">
                {{ $approved_loans->links() }}
            </div>
        @endif
    </div>
</div>