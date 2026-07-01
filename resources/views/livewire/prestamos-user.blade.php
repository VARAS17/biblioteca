<div class="p-6">
    <div class="max-w-5xl mx-auto">
        <!-- ENCABEZADO DE PERFIL -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 mb-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center space-x-4">
                <div class="h-16 w-16 rounded-2xl bg-blue-900 flex items-center justify-center text-white text-2xl font-black shadow-lg">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-black text-gray-900">{{ Auth::user()->name }}</h2>
                    <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">{{ Auth::user()->roles->first()->name ?? 'Estudiante' }} | {{ Auth::user()->institutional_code }}</p>
                </div>
            </div>
            <div class="flex space-x-4">
                <div class="text-center px-6 py-3 bg-blue-50 rounded-2xl border border-blue-100">
                    <p class="text-[10px] font-bold text-blue-700 uppercase mb-1">Libros en mano</p>
                    <p class="text-xl font-black text-blue-900">{{ $active_count }} / {{ Auth::user()->max_books }}</p>
                </div>
                <div class="text-center px-6 py-3 {{ $real_debt > 0 ? 'bg-red-50 border-red-100' : 'bg-green-50 border-green-100' }} rounded-2xl border">
                    <p class="text-[10px] font-bold {{ $real_debt > 0 ? 'text-red-700' : 'text-green-700' }} uppercase mb-1">Deuda Total</p>
                    <p class="text-xl font-black {{ $real_debt > 0 ? 'text-red-900' : 'text-green-900' }}">S/ {{ number_format($real_debt, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- TABS -->
        <div class="flex space-x-1 bg-gray-200 p-1 rounded-xl w-fit mb-6">
            <button wire:click="$set('tab', 'activos')" class="px-6 py-2 text-xs font-bold rounded-lg transition {{ $tab === 'activos' ? 'bg-white text-blue-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                MIS PRÉSTAMOS ACTIVOS
            </button>
            <button wire:click="$set('tab', 'historial')" class="px-6 py-2 text-xs font-bold rounded-lg transition {{ $tab === 'historial' ? 'bg-white text-blue-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                HISTORIAL DE LECTURA
            </button>
        </div>

        <!-- LISTADO -->
        <div class="space-y-4">
            @forelse($loans as $loan)
            @php
                // copy puede ser null si aún está en pending
                $title = $loan->copy?->book->title ?? $loan->book?->title ?? '—';
                $inventoryCode = $loan->copy?->inventory_code ?? null;
            @endphp
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 hover:border-blue-200 transition">
                
                <div class="flex items-center space-x-4 flex-1">
                    <div class="p-3 bg-gray-50 rounded-xl text-gray-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-800">{{ $title }}</h4>
                        @if($inventoryCode)
                            <p class="text-[10px] text-gray-400 font-mono uppercase tracking-tighter">CÓDIGO: {{ $inventoryCode }}</p>
                        @else
                            <p class="text-[10px] text-amber-500 font-bold uppercase tracking-tighter">Ejemplar por asignar</p>
                        @endif
                    </div>
                </div>

                {{-- INDICADOR DE FLUJO (solo tab activos) --}}
                @if($tab === 'activos')
                <div class="flex items-center gap-1.5">
                    {{-- Paso 1: Solicitado --}}
                    <div class="flex flex-col items-center gap-1">
                        <div class="h-2.5 w-2.5 rounded-full {{ in_array($loan->status, ['pending','approved','active']) ? 'bg-stone-800' : 'bg-stone-200' }}"></div>
                        <span class="text-[8px] font-black uppercase text-stone-400">Solicitado</span>
                    </div>
                    <div class="h-px w-5 {{ in_array($loan->status, ['approved','active']) ? 'bg-stone-800' : 'bg-stone-200' }}"></div>
                    {{-- Paso 2: Catálogo --}}
                    <div class="flex flex-col items-center gap-1">
                        <div class="h-2.5 w-2.5 rounded-full {{ in_array($loan->status, ['approved','active']) ? 'bg-stone-800' : 'bg-stone-200' }}"></div>
                        <span class="text-[8px] font-black uppercase text-stone-400">Catálogo</span>
                    </div>
                    <div class="h-px w-5 {{ $loan->status === 'active' ? 'bg-stone-800' : 'bg-stone-200' }}"></div>
                    {{-- Paso 3: Entregado --}}
                    <div class="flex flex-col items-center gap-1">
                        <div class="h-2.5 w-2.5 rounded-full {{ $loan->status === 'active' ? 'bg-green-500' : 'bg-stone-200' }}"></div>
                        <span class="text-[8px] font-black uppercase text-stone-400">Entregado</span>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-6 text-center">
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase">Fecha Solicitud</p>
                        <p class="text-xs font-medium text-gray-700">{{ $loan->loan_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        @if($tab === 'activos')
                            <p class="text-[9px] font-bold text-gray-400 uppercase">Devolver antes de</p>
                            <p class="text-xs font-black {{ $loan->status === 'active' && $loan->return_date < now() ? 'text-red-600' : 'text-blue-700' }}">
                                {{ $loan->return_date->format('d/m/Y') }}
                            </p>
                        @else
                            <p class="text-[9px] font-bold text-gray-400 uppercase">Devuelto el</p>
                            <p class="text-xs font-medium text-gray-700">{{ $loan->actual_return_date?->format('d/m/Y') ?? '—' }}</p>
                        @endif
                    </div>
                </div>

                <div class="w-full md:w-36 text-center md:text-right">
                    @if($tab === 'activos')
                        @if($loan->status === 'pending')
                            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-[10px] font-black uppercase">En espera</span>
                        @elseif($loan->status === 'approved')
                            <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-[10px] font-black uppercase">Listo · Pasa a recoger</span>
                        @elseif($loan->return_date < now())
                            <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-[10px] font-bold uppercase animate-pulse">Vencido</span>
                        @else
                            @php $days = ceil(now()->diffInDays($loan->return_date, false)); @endphp
                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-[10px] font-bold uppercase">Quedan {{ $days }} días</span>
                        @endif
                    @else
                        @if($loan->status === 'rejected')
                            <span class="text-[10px] font-black text-red-500 uppercase">Solicitud rechazada</span>
                        @elseif($loan->fine && $loan->fine->amount > 0)
                            <span class="text-xs font-black text-red-600">S/ {{ number_format($loan->fine->amount, 2) }}</span>
                        @else
                            <span class="text-[10px] font-bold text-green-600 uppercase">Sin multas</span>
                        @endif
                    @endif
                </div>

            </div>
            @empty
            <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                <p class="text-gray-400 italic">No tienes registros en esta sección.</p>
            </div>
            @endforelse

            <div class="mt-4">{{ $loans->links() }}</div>
        </div>

        @if($real_debt > 10)
        <div class="mt-8 p-4 bg-red-600 rounded-2xl text-white flex items-center shadow-lg shadow-red-200">
            <svg class="h-8 w-8 mr-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <div>
                <p class="font-bold uppercase text-xs">Atención: Usuario Bloqueado</p>
                <p class="text-sm opacity-90 italic">Tu deuda supera los S/ 10.00. No podrás solicitar nuevos préstamos hasta que canceles en secretaría.</p>
            </div>
        </div>
        @endif

    </div>
</div>