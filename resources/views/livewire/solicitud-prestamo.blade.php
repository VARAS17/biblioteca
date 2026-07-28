<div class="p-6">

    {{-- HEADER --}}
    <div class="mb-8">
        <p class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-1">Encargado de Usuarios</p>
        <h2 class="text-2xl font-black text-stone-900 tracking-tight">Nueva Solicitud de Préstamo</h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- FORMULARIO --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- PASO 1: USUARIO --}}
            <div class="bg-white rounded-2xl shadow-sm border border-stone-100 p-6">
                <p class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-4">
                    Paso 1 — Identificar Solicitante
                </p>
                <div class="flex gap-3">
                    <div class="flex-1">
                        <input wire:model="search_user" wire:keydown.enter="findUser"
                            type="text" placeholder="Código institucional o DNI..."
                            class="w-full px-4 py-2.5 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-stone-400 focus:outline-none">
                        @error('search_user')
                            <p class="text-xs text-red-600 font-bold mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <button wire:click="findUser"
                        class="bg-stone-800 hover:bg-black text-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition">
                        Buscar
                    </button>
                </div>

                @if($selectedUser)
                <div class="mt-4 flex items-center gap-4 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center text-sm font-black text-green-700">
                        {{ substr($selectedUser->name, 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-stone-800">{{ $selectedUser->name }}</p>
                        <p class="text-[10px] font-mono text-blue-700">{{ $selectedUser->institutional_code }}</p>
                    </div>
                    <span class="text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded-full
                        {{ $selectedUser->hasRole('docente') ? 'bg-blue-100 text-blue-700' : 'bg-stone-100 text-stone-500' }}">
                        {{ $selectedUser->hasRole('docente') ? 'Docente · 14 días' : 'Alumno · 7 días' }}
                    </span>
                    <button wire:click="$set('selectedUser', null)" class="text-stone-300 hover:text-red-400 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
                @endif
            </div>

            {{-- PASO 2: LIBRO --}}
            <div class="bg-white rounded-2xl shadow-sm border border-stone-100 p-6 {{ !$selectedUser ? 'opacity-50 pointer-events-none' : '' }}">
                <p class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-4">
                    Paso 2 — Libro Solicitado
                </p>
                <div class="flex gap-3">
                    <div class="flex-1">
                        <input wire:model="search_book" wire:keydown.enter="findBook"
                            type="text" placeholder="ISBN o título del libro..."
                            class="w-full px-4 py-2.5 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-stone-400 focus:outline-none">
                        @error('search_book')
                            <p class="text-xs text-red-600 font-bold mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <button wire:click="findBook"
                        class="bg-stone-800 hover:bg-black text-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition">
                        Buscar
                    </button>
                </div>

                @if($selectedBook)
                <div class="mt-4 flex items-center gap-4 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <div class="h-10 w-10 bg-stone-100 rounded-xl flex items-center justify-center">
                        <svg class="h-5 w-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-stone-800 leading-tight">{{ $selectedBook->title }}</p>
                        <p class="text-[10px] text-stone-400 mt-0.5">{{ $selectedBook->category->name ?? '—' }}</p>
                    </div>
                    <button wire:click="$set('selectedBook', null)" class="text-stone-300 hover:text-red-400 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>
                @endif
            </div>

            {{-- CONFIRMAR --}}
            @if($selectedUser && $selectedBook)
            <button wire:click="processRequest"
                wire:confirm="¿Registrar solicitud de '{{ $selectedBook->title }}' para {{ $selectedUser->name }}?"
                class="w-full py-4 bg-stone-800 hover:bg-black text-white rounded-2xl font-black text-sm uppercase tracking-widest transition">
                Registrar Solicitud →
            </button>
            @endif
        </div>

        {{-- PANEL DERECHO: ÚLTIMAS SOLICITUDES --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-stone-100 p-6">
                <p class="text-[10px] font-black text-stone-400 uppercase tracking-widest mb-4">
                    Últimas Solicitudes
                </p>
                <div class="space-y-3">
                    @forelse($recent_requests as $req)
                    <div class="p-3 bg-stone-50 rounded-xl border border-stone-100">
                        <p class="text-xs font-bold text-stone-800 leading-tight">{{ $req->book?->title ?? $req->copy?->book?->title ?? '—' }}</p>
                        <p class="text-[10px] text-stone-400 mt-1">{{ $req->user->name }}</p>
                        <div class="flex items-center gap-1.5 mt-2">
                            <div class="h-1.5 w-1.5 rounded-full bg-amber-400"></div>
                            <span class="text-[9px] font-black uppercase tracking-widest text-amber-600">Pendiente catálogo</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-stone-300 text-center py-6 font-bold uppercase tracking-widest">Sin solicitudes recientes</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>