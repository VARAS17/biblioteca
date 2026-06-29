<div class="p-6">
    <!-- Encabezado de Búsqueda -->
    <div class="mb-10 text-center">
        <h2 class="text-4xl font-black text-blue-900 mb-2 tracking-tighter uppercase">Catálogo Bibliográfico</h2>
        <p class="text-gray-500 text-sm mb-8 italic">Explora el material disponible en la Escuela de Informática</p>
        
        <div class="max-w-2xl mx-auto relative">
            <input wire:model.live.debounce.300ms="search" type="text" 
                placeholder="Busca por Título, Autor o ISBN..."
                class="w-full py-4 pl-14 pr-6 rounded-2xl border-none shadow-xl focus:ring-4 focus:ring-blue-100 text-lg transition-all">
            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                <svg class="h-6 w-6 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
        </div>
    </div>

    <!-- Grid de Libros -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @foreach($books as $book)
            @php 
                $availableCopies = $book->copies->where('status', 'available')->count(); 
            @endphp
            <div class="bg-white rounded-3xl shadow-sm hover:shadow-2xl transition-all duration-300 border border-gray-100 flex flex-col group">
                <div class="p-6 flex-1">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-[9px] font-black uppercase px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full tracking-widest">
                            {{ $book->category->name }}
                        </span>
                        <div class="flex items-center space-x-1">
                            <div class="h-2 w-2 rounded-full {{ $availableCopies > 0 ? 'bg-green-500' : 'bg-red-500' }}"></div>
                            <span class="text-[10px] font-bold {{ $availableCopies > 0 ? 'text-green-600' : 'text-red-600' }} uppercase">
                                {{ $availableCopies }} Disponibles
                            </span>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 leading-tight mb-2 group-hover:text-blue-900 transition-colors">{{ $book->title }}</h3>
                    <p class="text-xs text-gray-400 font-medium">ISBN: {{ $book->isbn }}</p>
                    <p class="text-xs text-gray-600 mt-3 font-semibold">Autores: <span class="font-normal">{{ $book->authors->pluck('name')->implode(', ') }}</span></p>
                </div>

                <div class="p-4 bg-gray-50 border-t border-gray-100 flex space-x-2 rounded-b-3xl">
                    <button wire:click="showDetails({{ $book->id }})" class="flex-1 bg-white border border-gray-200 text-gray-700 py-2.5 rounded-xl text-[10px] font-black uppercase hover:bg-gray-100 transition shadow-sm">
                        Detalles
                    </button>

                    @role('docente')
                        @if($availableCopies > 0)
                            <button wire:click="reserveBook({{ $book->id }})" class="flex-1 bg-blue-900 text-white py-2.5 rounded-xl text-[10px] font-black uppercase hover:bg-blue-800 transition shadow-md shadow-blue-200">
                                Apartar Libro
                            </button>
                        @endif
                    @endrole
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-12">
        {{ $books->links() }}
    </div>

    <!-- MODAL DE DETALLES -->
    @if($selected_book)
    <div class="fixed inset-0 z-[100] flex items-center justify-center bg-blue-900/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl overflow-hidden">
            <div class="p-10">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h2 class="text-3xl font-black text-gray-900 mb-2">{{ $selected_book->title }}</h2>
                        <p class="text-blue-700 font-bold tracking-widest text-xs uppercase">Detalles del Material</p>
                    </div>
                    <button wire:click="$set('selected_book', null)" class="bg-gray-100 text-gray-400 h-10 w-10 rounded-full flex items-center justify-center">✕</button>
                </div>
                <!-- ... resto del contenido del modal ... -->
                <button wire:click="$set('selected_book', null)" class="mt-6 w-full py-3 bg-gray-900 text-white rounded-2xl font-bold">Cerrar</button>
            </div>
        </div>
    </div>
    @endif
</div>
