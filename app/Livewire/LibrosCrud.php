<?php

namespace App\Livewire;

use App\Models\{Book, Category, Author, Copy};
use Livewire\{Component, WithPagination};
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('layouts.public-layout')]
class LibrosCrud extends Component
{
    use WithPagination;

    // --- PARÁMETROS DE CONFIGURACIÓN ---
    const PAGINATION_COUNT = 10;
    const AUTHOR_SEARCH_LIMIT = 5;
    const STATUS_AVAILABLE = 'available';
    const MIN_SEARCH_CHARS = 2;

    // --- PROPIEDADES DE ESTADO ---
    public $view = 'list';
    public $search = '';

    // --- PROPIEDADES DEL MODELO LIBRO ---
    public $book_id;
    public $title, $isbn, $category_id, $editorial, $published_year, $description;
    
    // --- PROPIEDADES DE RELACIONES Y BÚSQUEDA ---
    public $selected_authors = [];
    public $author_search = '';

    // --- PROPIEDADES DE EJEMPLARES ---
    public $copy_inventory_code, $copy_location;
    public $copy_status = self::STATUS_AVAILABLE;
    public $selected_book;

    // --- PROPIEDADES GESTIÓN SECUNDARIA ---
    public $name, $model_id, $is_editing_secondary = false;

    /**
     * Reglas de validación parametrizadas
     */
    protected function rules() {
        return [
            'title'          => 'required|string|min:3|max:255',
            'isbn'           => ['required', 'string', Rule::unique('books', 'isbn')->ignore($this->book_id)],
            'category_id'    => 'required|exists:categories,id',
            'editorial'      => 'required|string|min:3|max:150',
            'published_year' => 'required|integer|digits:4|min:1000|max:' . (date('Y') + 1),
            'description'    => 'nullable|string|max:2000',
            'selected_authors' => 'required|array|min:1',
        ];
    }

    /**
     * NAVEGACIÓN Y CARGA DE DATOS
     */
    public function setView($viewName, $id = null)
    {
        $this->resetErrorBag();
        $this->view = $viewName;

        match ($viewName) {
            'form'   => $this->handleFormView($id),
            'copies' => $this->handleCopiesView($id),
            default  => $this->resetBookFields(),
        };
    }

    private function handleFormView($id) {
        if ($id) {
            $book = Book::findOrFail($id);
            $this->book_id        = $id;
            $this->title          = $book->title;
            $this->isbn           = $book->isbn;
            $this->category_id    = $book->category_id;
            $this->editorial      = $book->editorial;
            $this->published_year = $book->published_year;
            $this->description    = $book->description;
            $this->selected_authors = $book->authors->pluck('id')->toArray();
        } else {
            $this->resetBookFields();
        }
    }

    private function handleCopiesView($id) {
        $this->selected_book = Book::with('copies')->findOrFail($id);
        $this->reset(['copy_inventory_code', 'copy_location']);
    }

    /**
     * LÓGICA DE EJEMPLARES (STOCK)
     */
    public function saveCopy()
    {
        $this->validate([
            'copy_inventory_code' => ['required', Rule::unique('copies', 'inventory_code')],
            'copy_location'       => 'nullable|string|max:100'
        ]);

        Copy::create([
            'book_id'        => $this->selected_book->id,
            'inventory_code' => $this->copy_inventory_code,
            'location'       => $this->copy_location,
            'status'         => self::STATUS_AVAILABLE
        ]);

        $this->handleCopiesView($this->selected_book->id);
        $this->dispatch('notify', '¡Ejemplar añadido correctamente!');
    }

    public function deleteCopy($id)
    {
        Copy::findOrFail($id)->delete();
        $this->handleCopiesView($this->selected_book->id);
        $this->dispatch('notify', 'Ejemplar eliminado.');
    }

    /**
     * CRUD LIBROS
     */
    public function saveBook()
    {
        $data = $this->validate();

        // Extraemos autores para el sync y limpiamos el array para el updateOrCreate
        $authors = $data['selected_authors'];
        unset($data['selected_authors']);

        $book = Book::updateOrCreate(['id' => $this->book_id], $data);
        
        $book->authors()->sync($authors);
        
        $this->dispatch('notify', 'Libro actualizado con éxito');
        $this->setView('list');
    }

    public function deleteBook($id)
    {
        Book::findOrFail($id)->delete();
        $this->dispatch('notify', 'Libro movido a papelera');
    }

    /**
     * GESTIÓN DE AUTORES (RELACIÓN)
     */
    public function addAuthor($id) {
        if (!in_array($id, $this->selected_authors)) {
            $this->selected_authors[] = $id;
        }
        $this->author_search = '';
    }

    public function removeAuthor($id) {
        $this->selected_authors = array_diff($this->selected_authors, [$id]);
    }

    /**
     * GESTIÓN RÁPIDA DE CATEGORÍAS/AUTORES (MODALES)
     */
    public function saveSecondary($type) {
        $this->validate(['name' => 'required|min:2|max:100']);

        if ($type === 'category') {
            Category::updateOrCreate(['id' => $this->model_id], [
                'name' => $this->name, 
                'slug' => Str::slug($this->name)
            ]);
        } else {
            Author::updateOrCreate(['id' => $this->model_id], [
                'name' => $this->name
            ]);
        }

        $this->reset(['name', 'model_id', 'is_editing_secondary']);
        $this->dispatch('notify', 'Registro actualizado');
    }

    public function editSecondary($type, $id) {
        $model = ($type === 'category') ? Category::findOrFail($id) : Author::findOrFail($id);
        $this->model_id = $id; 
        $this->name = $model->name; 
        $this->is_editing_secondary = true;
    }

    private function resetBookFields() {
        $this->reset([
            'book_id', 'title', 'isbn', 'category_id', 'selected_authors',
            'editorial', 'published_year', 'description'
        ]);
    }

    /**
     * RENDERIZADO Y FILTROS
     */
    public function render()
    {
        // Parametrización de búsqueda de autores
        $available_authors = strlen($this->author_search) >= self::MIN_SEARCH_CHARS 
            ? Author::where('name', 'like', "%{$this->author_search}%")
                ->whereNotIn('id', $this->selected_authors)
                ->limit(self::AUTHOR_SEARCH_LIMIT)
                ->get() 
            : [];

        return view('livewire.libros-crud', [
            'books' => Book::query()
                ->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('isbn', 'like', '%' . $this->search . '%')
                ->withCount('copies')
                ->latest()
                ->paginate(self::PAGINATION_COUNT),
                
            'categories' => Category::orderBy('name')->get(),
            'authors_list' => Author::orderBy('name')->get(),
            'available_authors' => $available_authors,
            'selected_authors_models' => Author::whereIn('id', $this->selected_authors)->get()
        ]);
    }
}