<?php

namespace App\Livewire\Admin;

use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Livewire\Component;
use Livewire\WithFileUploads; // Necesario para subir imágenes
use Livewire\WithPagination;

class BookManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Campos del formulario
    public $title, $author, $isbn, $synopsis, $category_id, $publisher_id, $stock_total;
    public $image; // Temporal para la subida
    
    public $isEditing = false;
    public $bookIdToEdit = null;

    // Reglas de validación
    protected $rules = [
        'title' => 'required|min:3',
        'author' => 'required',
        'category_id' => 'required|exists:categories,id',
        'publisher_id' => 'required|exists:publishers,id',
        'stock_total' => 'required|integer|min:1',
        'image' => 'nullable|image|max:1024', // Máximo 1MB
    ];

    public function render()
    {
        return view('livewire.admin.book-manager', [
            'books' => Book::latest()->paginate(10),
            'categories' => Category::all(),
            'publishers' => Publisher::all(),
        ]); // Usamos el layout principal de Breeze
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'synopsis' => $this->synopsis,
            'category_id' => $this->category_id,
            'publisher_id' => $this->publisher_id,
            'stock_total' => $this->stock_total,
        ];

        // Manejo de Imagen
        if ($this->image) {
            // Guarda en storage/app/public/books
            $path = $this->image->store('books', 'public');
            $data['image_path'] = $path;
        }

        if ($this->isEditing) {
            $book = Book::find($this->bookIdToEdit);
            $book->update($data);
            session()->flash('message', 'Libro actualizado correctamente.');
        } else {
            Book::create($data);
            session()->flash('message', 'Libro registrado correctamente.');
        }

        $this->resetInput();
    }

    public function edit($id)
    {
        $book = Book::findOrFail($id);
        $this->bookIdToEdit = $id;
        $this->isEditing = true;

        $this->title = $book->title;
        $this->author = $book->author;
        $this->isbn = $book->isbn;
        $this->synopsis = $book->synopsis;
        $this->category_id = $book->category_id;
        $this->publisher_id = $book->publisher_id;
        $this->stock_total = $book->stock_total;
    }

    public function delete($id)
    {
        Book::find($id)->delete();
        session()->flash('message', 'Libro eliminado.');
    }

    public function resetInput()
    {
        $this->reset(['title', 'author', 'isbn', 'synopsis', 'category_id', 'publisher_id', 'stock_total', 'image', 'isEditing', 'bookIdToEdit']);
    }
}