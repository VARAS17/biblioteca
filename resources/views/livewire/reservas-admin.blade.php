<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-black text-blue-900 uppercase tracking-tighter">Panel de Apartados (Docentes)</h2>
        <div class="relative w-64">
            <input wire:model.live="search" type="text" placeholder="Buscar docente..." class="w-full pl-10 pr-4 py-2 border-gray-300 rounded-lg text-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Docente</th>
                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Libro Apartado</th>
                    <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Fecha Solicitud</th>
                    <th class="px-6 py-3 text-right text-[10px] font-bold text-gray-500 uppercase tracking-widest">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reservations as $res)
                <tr class="hover:bg-blue-50/30 transition">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900">{{ $res->user->name }}</div>
                        <div class="text-[10px] text-blue-600 font-mono">{{ $res->user->institutional_code }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-700">{{ $res->book->title }}</div>
                        <div class="text-[10px] text-gray-400 uppercase">ISBN: {{ $res->book->isbn }}</div>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($res->reservation_date)->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button 
                            wire:click="fulfillReservation({{ $res->id }})" 
                            wire:confirm="¿Confirmas que el docente está recogiendo el libro físico ahora?"
                            class="bg-blue-900 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-blue-800 transition shadow-md">
                            Procesar Entrega
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">No hay apartados pendientes por entregar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t bg-gray-50">
            {{ $reservations->links() }}
        </div>
    </div>
</div>