<div class="p-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- LISTA DE LIBROS PRESTADOS -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2rem] shadow-xl overflow-hidden border border-gray-100">
                <div class="p-6 bg-gray-50 border-b flex flex-col md:flex-row justify-between items-center gap-4">
                    <h3 class="text-lg font-black text-blue-900 uppercase tracking-tighter">Libros en Circulación</h3>
                    <div class="relative w-full md:w-72">
                        <input wire:model.live="search_active" type="text" placeholder="Alumno, libro o código..." 
                            class="w-full pl-10 pr-4 py-2 border-none bg-white rounded-xl shadow-sm text-sm focus:ring-2 focus:ring-blue-900">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                    </div>
                </div>

                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Material</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Vencimiento</th>
                            <th class="px-6 py-4 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($active_loans as $loan)
                        <tr class="hover:bg-blue-50/50 transition {{ $selectedLoan && $selectedLoan->id == $loan->id ? 'bg-blue-50' : '' }}">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $loan->copy->book->title }}</div>
                                <div class="text-[10px] text-gray-400">{{ $loan->user->name }} ({{ $loan->copy->inventory_code }})</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold {{ $loan->return_date < now() ? 'text-red-600' : 'text-gray-600' }}">
                                    {{ $loan->return_date->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="selectLoan({{ $loan->id }})" class="bg-blue-900 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-blue-800 transition">
                                    Procesar
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-10 text-center text-gray-400">No hay libros prestados con ese criterio.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">{{ $active_loans->links() }}</div>
            </div>
        </div>

        <!-- PANEL DE RECEPCIÓN (DERECHA) -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-8 rounded-[2rem] shadow-xl border-t-8 {{ $fine_amount > 0 ? 'border-red-600' : 'border-green-600' }} transition-colors duration-500">
                <h3 class="text-xl font-black text-gray-900 mb-6 uppercase tracking-tighter">Detalle de Entrega</h3>

                @if($selectedLoan)
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Libro</p>
                            <p class="text-sm font-bold text-blue-900 leading-tight">{{ $selectedLoan->copy->book->title }}</p>
                            
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-4 mb-1">Alumno / Docente</p>
                            <p class="text-sm font-bold text-gray-800">{{ $selectedLoan->user->name }}</p>
                        </div>

                        <!-- LÓGICA DE MULTA VISUAL -->
                        @if($fine_amount > 0)
                            <div class="p-6 rounded-3xl bg-red-600 text-white shadow-lg shadow-red-200">
                                <p class="text-[10px] font-black uppercase tracking-widest mb-1 opacity-80">Multa por Retraso</p>
                                <div class="flex items-end space-x-2">
                                    <span class="text-3xl font-black">S/ {{ number_format($fine_amount, 2) }}</span>
                                    <span class="text-xs font-bold mb-1">({{ $days_late }} días)</span>
                                </div>
                                <p class="text-[10px] mt-2 italic opacity-90">RN-08: S/ 0.50 por día.</p>
                            </div>
                        @else
                            <div class="p-6 rounded-3xl bg-green-50 border border-green-200 text-green-700 text-center">
                                <p class="text-xs font-black uppercase tracking-widest">¡Entrega a Tiempo!</p>
                                <p class="text-[10px] font-bold mt-1 uppercase">No se genera mora</p>
                            </div>
                        @endif

                        <button wire:click="processReturn" 
                            wire:confirm="¿Confirmas la recepción física del libro y el registro de la devolución?"
                            class="w-full py-4 bg-gray-900 hover:bg-black text-white rounded-2xl font-black text-sm transition uppercase tracking-widest">
                            Confirmar Devolución
                        </button>
                        
                        <button wire:click="$set('selectedLoan', null)" class="w-full text-[10px] text-gray-400 hover:text-red-500 uppercase font-bold tracking-widest">
                            Cancelar y volver
                        </button>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="h-20 w-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-10 w-10 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest leading-relaxed">Selecciona un préstamo de la lista para calcular multas y procesar el retorno.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>