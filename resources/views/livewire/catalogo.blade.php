<div class="max-w-7xl mx-auto p-6 bg-gray-50 min-h-screen">
    
    <!-- Encabezado del Catálogo -->
    <div class="mb-10 text-center md:text-left">
        <h1 class="text-4xl font-black text-gray-900 tracking-tighter">
            Catálogo <span class="text-indigo-600">Digital</span>
        </h1>
        <p class="text-gray-500 font-medium">Explora nuestra colección de recursos y obras literarias.</p>
    </div>

    <!-- Barra de Búsqueda -->
    <div class="relative mb-10 max-w-2xl mx-auto md:mx-0">
        <input wire:model.live.debounce.300ms="search" type="text" 
            placeholder="Buscar por título, autor o ISBN..." 
            class="w-full pl-14 pr-6 py-4 bg-white border-none rounded-3xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all text-lg font-medium">
        <svg class="w-6 h-6 text-indigo-400 absolute left-5 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </div>

    <!-- Grid de Libros (Aquí se usa $book-> porque es un objeto de la colección) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @forelse($books as $book)
            <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group flex flex-col h-full border border-gray-100">
                <!-- Portada -->
                <div class="relative h-72 overflow-hidden bg-gray-200">
                    @if($book->cover)
                        <img src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-indigo-50 text-indigo-200">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif
                    
                    <div class="absolute top-4 left-4">
                        <span class="bg-white/90 backdrop-blur px-3 py-1 rounded-full text-[10px] font-black text-indigo-600 uppercase tracking-tighter shadow-sm">
                            {{ $book->category->name }}
                        </span>
                    </div>
                </div>

                <!-- Info -->
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-lg font-black text-gray-900 leading-tight group-hover:text-indigo-600 transition mb-1 h-12 overflow-hidden">
                        {{ $book->title }}
                    </h3>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-4 truncate">
                        {{ $book->authors->pluck('name')->implode(', ') }}
                    </p>

                    <!-- Botones -->
                    <div class="mt-auto grid grid-cols-2 gap-3">
                        <button wire:click="showDetails({{ $book->id }})" 
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-2xl text-xs transition-all uppercase tracking-widest">
                            Detalles
                        </button>
                        
                        @role('docente')
                        <button wire:click="reserveBook({{ $book->id }})" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-black py-3 rounded-2xl text-xs transition-all uppercase tracking-widest shadow-lg shadow-indigo-100">
                            Apartar
                        </button>
                        @endrole
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center">
                <p class="text-gray-400 font-bold italic">No se encontraron resultados.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $books->links() }}
    </div>

    <!-- MODAL DE DETALLES (Aquí se usa $selected_book[''] porque es un ARRAY) -->
    @if($selected_book)
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
        <div class="bg-white w-full max-w-4xl rounded-[3rem] shadow-2xl overflow-hidden relative animate-in slide-in-from-bottom-8 duration-300">
            
            <!-- Botón Cerrar Corregido -->
            <button wire:click="closeDetails" class="absolute top-6 right-6 p-3 bg-gray-100 rounded-2xl text-gray-400 hover:text-red-500 transition-all z-10 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-2">
                <!-- Columna Portada -->
                <div class="h-64 md:h-auto bg-gray-100">
                    @if($selected_book['cover'])
                        <img src="{{ asset('storage/' . $selected_book['cover']) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-indigo-50 text-indigo-200">
                            <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif
                </div>

                <!-- Columna Información -->
                <div class="p-10">
                    <span class="bg-indigo-100 text-indigo-600 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest">
                        {{ $selected_book['category']['name'] }}
                    </span>
                    <h2 class="text-3xl font-black text-gray-900 mt-4 leading-tight">{{ $selected_book['title'] }}</h2>
                    <p class="text-indigo-600 font-bold mt-2">
                        {{ collect($selected_book['authors'])->pluck('name')->implode(', ') }}
                    </p>

                    <div class="grid grid-cols-2 gap-6 my-8 pt-8 border-t border-gray-100 text-sm">
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">ISBN</p>
                            <p class="font-bold text-gray-700 font-mono">{{ $selected_book['isbn'] }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Editorial</p>
                            <p class="font-bold text-gray-700">{{ $selected_book['editorial'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Año</p>
                            <p class="font-bold text-gray-700">{{ $selected_book['published_year'] ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="mb-8 h-32 overflow-y-auto pr-2 custom-scrollbar">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Descripción</p>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            {{ $selected_book['description'] ?? 'No hay una descripción disponible.' }}
                        </p>
                    </div>

                    @role('docente')
                    <button wire:click="reserveBook({{ $selected_book['id'] }})" 
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 rounded-2xl transition-all shadow-xl shadow-indigo-100 uppercase tracking-widest text-sm">
                        Confirmar Apartado
                    </button>
                    @endrole
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:init', () => {
       Livewire.on('notify', (event) => {
           alert(event[0]); // O puedes usar un toast más elegante
       });
    });
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>