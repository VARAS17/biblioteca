<div class="max-w-6xl mx-auto p-4 md:p-8 bg-gray-50 min-h-screen">
    
    <!-- 1. BARRA DE NAVEGACIÓN PRINCIPAL (Heurística #4: Consistencia y Estándares) -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tighter">
                Biblioteca<span class="text-indigo-600">Pro</span>
            </h1>
            <p class="text-sm text-gray-500 font-medium">Gestión integral de inventario y catálogo</p>
        </div>
        
        <nav class="flex bg-gray-200/50 p-1.5 rounded-2xl shadow-inner">
            <button wire:click="setView('list')" 
                class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ in_array($view, ['list', 'form', 'copies']) ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                📚 Libros
            </button>
            <button wire:click="setView('categories')" 
                class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $view === 'categories' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                📁 Categorías
            </button>
            <button wire:click="setView('authors')" 
                class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $view === 'authors' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                👤 Autores
            </button>
        </nav>
    </div>

    <!-- 2. FEEDBACK DE ESTADO (Heurística #1: Visibilidad del estado del sistema) -->
    <div wire:loading class="fixed top-6 right-6 z-50">
        <div class="flex items-center gap-3 bg-white shadow-2xl border border-gray-100 px-6 py-3 rounded-2xl">
            <div class="animate-spin h-5 w-5 border-3 border-indigo-600 border-t-transparent rounded-full"></div>
            <span class="text-sm font-bold text-gray-700">Sincronizando...</span>
        </div>
    </div>

    <!-- VISTA 1: LISTADO DE LIBROS -->
    @if($view === 'list')
    <div class="animate-in fade-in slide-in-from-bottom-4 duration-500 space-y-6">
        <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
            <div class="relative flex-1 w-full">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por título, ISBN o editorial..." 
                    class="w-full pl-14 pr-4 py-4 bg-white border-none rounded-3xl shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                <svg class="w-6 h-6 text-gray-300 absolute left-5 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button wire:click="setView('form')" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-3xl font-black shadow-xl shadow-indigo-100 transition-all transform hover:-translate-y-1">
                + Nuevo Libro
            </button>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-50">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-8 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Obra / ISBN</th>
                        <th class="px-8 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Editorial e Info</th>
                        <th class="px-8 py-5 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Disponibilidad</th>
                        <th class="px-8 py-5 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($books as $book)
                    <tr class="hover:bg-indigo-50/30 transition group">
                        <td class="px-8 py-6">
                            <h4 class="font-black text-gray-900 text-lg group-hover:text-indigo-600 transition leading-tight">{{ $book->title }}</h4>
                            <p class="text-xs text-gray-400 font-mono mt-1">ISBN: {{ $book->isbn }}</p>
                        </td>
                        <td class="px-8 py-6 text-sm text-gray-600">
                            <div class="flex flex-col">
                                <span class="font-bold text-indigo-600 text-[10px] uppercase tracking-wider">{{ $book->category->name ?? 'Sin categoría' }}</span>
                                <span class="font-medium">{{ $book->editorial }} ({{ $book->published_year }})</span>
                                <span class="text-xs text-gray-400 italic mt-0.5 truncate max-w-[200px]">{{ $book->authors->pluck('name')->implode(', ') }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-black {{ $book->copies_count > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $book->copies_count }} Ejemplares
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right space-x-1">
                            <button wire:click="setView('copies', {{ $book->id }})" class="bg-gray-100 hover:bg-indigo-600 hover:text-white px-4 py-2 rounded-xl text-xs font-bold transition-all">Stock</button>
                            <button wire:click="setView('form', {{ $book->id }})" class="text-gray-400 hover:text-indigo-600 p-2 font-bold transition text-xs uppercase tracking-tighter">Editar</button>
                            <button onclick="confirm('¿Eliminar libro permanentemente?') || event.stopImmediatePropagation()" wire:click="deleteBook({{ $book->id }})" class="text-gray-300 hover:text-red-500 p-2 transition text-xs font-bold uppercase tracking-tighter">Borrar</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-8 py-24 text-center text-gray-400 italic">No se encontraron libros en el catálogo.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-6 bg-gray-50/50 border-t border-gray-100">{{ $books->links() }}</div>
        </div>
    </div>
    @endif

    <!-- VISTA 2: FORMULARIO DE LIBRO (Añadir/Editar) -->
    @if($view === 'form')
    <div class="max-w-4xl mx-auto bg-white p-10 rounded-[3rem] shadow-2xl border border-gray-100 animate-in slide-in-from-bottom-8 duration-500">
        <header class="flex justify-between items-center mb-10">
            <div>
                <h2 class="text-3xl font-black text-gray-900 tracking-tight">{{ $book_id ? 'Editar Libro' : 'Nuevo Registro' }}</h2>
                <p class="text-gray-400 text-sm font-medium">Complete la información bibliográfica detallada</p>
            </div>
            <button wire:click="setView('list')" class="p-3 bg-gray-50 rounded-2xl text-gray-400 hover:text-gray-900 transition-all hover:rotate-90">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </header>

        <form wire:submit.prevent="saveBook" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <div class="md:col-span-2">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Título de la Obra</label>
                <input wire:model="title" type="text" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-6 focus:ring-2 focus:ring-indigo-500 transition-all text-lg font-bold">
                @error('title') <span class="text-red-500 text-xs mt-2 ml-1 font-bold">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">ISBN</label>
                <input wire:model="isbn" type="text" placeholder="978-..." class="w-full bg-gray-50 border-none rounded-2xl py-4 px-6 focus:ring-2 focus:ring-indigo-500 transition-all">
                @error('isbn') <span class="text-red-500 text-xs mt-2 ml-1 font-bold">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Editorial</label>
                <input wire:model="editorial" type="text" placeholder="Ej: Anaya, Penguin..." class="w-full bg-gray-50 border-none rounded-2xl py-4 px-6 focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                @error('editorial') <span class="text-red-500 text-xs mt-2 ml-1 font-bold">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Categoría Literaria</label>
                <select wire:model="category_id" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-6 focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                    <option value="">Seleccione...</option>
                    @foreach($categories as $cat) <option value="{{ $cat->id }}">{{ $cat->name }}</option> @endforeach
                </select>
                @error('category_id') <span class="text-red-500 text-xs mt-2 ml-1 font-bold">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Año de Publicación</label>
                <input wire:model="published_year" type="number" class="w-full bg-gray-50 border-none rounded-2xl py-4 px-6 focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                @error('published_year') <span class="text-red-500 text-xs mt-2 ml-1 font-bold">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Resumen / Descripción</label>
                <textarea wire:model="description" rows="3" class="w-full bg-gray-50 border-none rounded-3xl py-4 px-6 focus:ring-2 focus:ring-indigo-500 transition-all"></textarea>
                @error('description') <span class="text-red-500 text-xs mt-2 ml-1 font-bold">{{ $message }}</span> @enderror
            </div>

            <!-- SELECTOR DE TAGS/AUTORES (Heurística #7: Flexibilidad y Eficiencia) -->
            <div class="md:col-span-2">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Autores vinculados</label>
                <div class="flex flex-wrap gap-2 mb-4 p-4 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200 min-h-[80px]">
                    @forelse($selected_authors_models as $s_author)
                    <span class="inline-flex items-center bg-indigo-600 text-white px-5 py-2 rounded-full text-sm font-bold shadow-md animate-in zoom-in duration-200">
                        {{ $s_author->name }}
                        <button type="button" wire:click="removeAuthor({{ $s_author->id }})" class="ml-3 hover:text-red-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                    @empty
                    <p class="text-gray-400 text-sm m-auto italic">Use el buscador inferior para añadir autores</p>
                    @endforelse
                </div>

                <div class="relative">
                    <input wire:model.live="author_search" type="text" placeholder="Escriba el nombre de un autor..." class="w-full bg-gray-100 border-none rounded-2xl py-4 px-12 focus:ring-2 focus:ring-indigo-500 transition-all">
                    <svg class="w-5 h-5 text-gray-400 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    
                    @if(!empty($available_authors))
                    <div class="absolute z-10 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl overflow-hidden animate-in fade-in duration-200">
                        @foreach($available_authors as $a_auth)
                        <button type="button" wire:click="addAuthor({{ $a_auth->id }})" class="w-full text-left px-6 py-4 hover:bg-indigo-50 flex justify-between items-center transition border-b border-gray-50 last:border-0">
                            <span class="font-bold text-gray-700">{{ $a_auth->name }}</span>
                            <span class="text-indigo-600 font-black text-xs tracking-tighter uppercase">+ Añadir</span>
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
                @error('selected_authors') <span class="text-red-500 text-xs mt-2 ml-1 font-bold">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2 pt-8 flex flex-col md:flex-row justify-between items-center gap-6 border-t border-gray-50">
                <button type="button" wire:click="setView('list')" class="text-gray-400 hover:text-gray-900 font-bold tracking-tight">Descartar cambios</button>
                <button type="submit" class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white px-16 py-5 rounded-[2rem] font-black shadow-xl shadow-green-100 transition-all transform hover:-translate-y-2">
                    {{ $book_id ? 'ACTUALIZAR LIBRO' : 'GUARDAR LIBRO' }}
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- VISTA 3: GESTIÓN DE EJEMPLARES (STOCK) -->
    @if($view === 'copies')
    <div class="animate-in slide-in-from-right duration-500">
        <header class="flex items-center gap-6 mb-10">
            <button wire:click="setView('list')" class="p-4 bg-white rounded-2xl shadow-sm text-gray-400 hover:text-indigo-600 transition hover:-translate-x-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <div>
                <h2 class="text-3xl font-black text-gray-900 leading-none mb-1">{{ $selected_book->title }}</h2>
                <p class="text-gray-500 font-medium">Inventario de ejemplares físicos registrados</p>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="bg-indigo-900 p-8 rounded-[3rem] shadow-2xl h-fit text-white">
                <h3 class="text-xl font-bold mb-6">Nuevo Ejemplar</h3>
                <form wire:submit.prevent="saveCopy" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-indigo-300 mb-2">Código Único (Barras/QR)</label>
                        <input wire:model="copy_inventory_code" type="text" placeholder="INV-0000" 
                            class="w-full bg-indigo-800 border-none rounded-2xl py-4 px-6 text-white placeholder-indigo-400 focus:ring-2 focus:ring-white transition-all font-mono">
                        @error('copy_inventory_code') <p class="text-red-300 text-xs mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-indigo-300 mb-2">Ubicación Física</label>
                        <input wire:model="copy_location" type="text" placeholder="Ej: Sala A - Estante 4" 
                            class="w-full bg-indigo-800 border-none rounded-2xl py-4 px-6 text-white placeholder-indigo-400 focus:ring-2 focus:ring-white transition-all font-bold">
                    </div>
                    <button type="submit" class="w-full bg-white text-indigo-900 py-4 rounded-2xl font-black hover:bg-indigo-50 transition-all shadow-lg">
                        Registrar en Stock
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($selected_book->copies as $copy)
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex justify-between items-center group hover:border-indigo-200 transition-all">
                    <div class="flex items-center gap-5">
                        <div class="p-4 bg-indigo-50 text-indigo-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        </div>
                        <div>
                            <p class="font-black text-gray-900 text-lg tracking-tight">{{ $copy->inventory_code }}</p>
                            <p class="text-xs text-gray-400 font-bold uppercase">{{ $copy->location ?? 'Global' }}</p>
                        </div>
                    </div>
                    <button onclick="confirm('¿Eliminar ejemplar?') || event.stopImmediatePropagation()" wire:click="deleteCopy({{ $copy->id }})" class="p-3 text-gray-200 hover:text-red-500 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
                @empty
                <div class="md:col-span-2 py-20 text-center bg-gray-100/50 rounded-[3rem] border-2 border-dashed border-gray-200">
                    <p class="text-gray-400 font-bold italic">No hay ejemplares registrados.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    <!-- VISTA 4 Y 5: GESTIÓN DE CATEGORÍAS Y AUTORES -->
    @if($view === 'categories' || $view === 'authors')
    @php $type = ($view === 'categories') ? 'category' : 'author'; @endphp
    <div class="max-w-2xl mx-auto animate-in slide-in-from-bottom-8 duration-500">
        <div class="bg-white p-10 rounded-[3rem] shadow-xl border border-gray-100 mb-10">
            <h3 class="text-2xl font-black mb-8 text-gray-900 leading-tight">Gestionar {{ $type === 'category' ? 'Categorías Literarias' : 'Directorio de Autores' }}</h3>
            
            <div class="flex gap-3">
                <input wire:model="name" type="text" placeholder="Nombre completo..." class="flex-1 bg-gray-50 border-none rounded-2xl py-4 px-6 focus:ring-2 focus:ring-indigo-500 font-bold">
                <button wire:click="saveSecondary('{{ $type }}')" class="bg-indigo-600 text-white px-8 rounded-2xl font-black hover:bg-indigo-700 transition shadow-lg">
                    {{ $is_editing_secondary ? 'Actualizar' : 'Añadir' }}
                </button>
            </div>
            @error('name') <span class="text-red-500 text-xs mt-3 block font-bold ml-1">{{ $message }}</span> @enderror
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-50">
            @foreach(($type === 'category' ? $categories : $authors_list) as $item)
            <div class="px-10 py-5 flex justify-between items-center hover:bg-indigo-50/20 transition-all group">
                <span class="font-bold text-gray-700 group-hover:text-indigo-600 transition">{{ $item->name }}</span>
                <div class="flex gap-2">
                    <button wire:click="editSecondary('{{ $type }}', {{ $item->id }})" class="text-xs font-black text-indigo-400 hover:text-indigo-700 uppercase p-2">Editar</button>
                    <button onclick="confirm('¿Estás seguro?') || event.stopImmediatePropagation()" wire:click="deleteSecondary('{{ $type }}', {{ $item->id }})" class="text-xs font-black text-gray-300 hover:text-red-500 uppercase p-2">Borrar</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

<!-- SCRIPT DE NOTIFICACIONES (Heurística #9: Ayuda a reconocer y recuperarse de errores) -->
<script>
    document.addEventListener('livewire:init', () => {
       Livewire.on('notify', (message) => {
           // Aquí podrías integrar un SweetAlert2 o Toastify para más elegancia
           alert(message);
       });
    });
</script>