<?php

namespace App\Livewire\Student;

use App\Models\Book;
use App\Models\Loan;
use Livewire\Component;
use Livewire\WithPagination;

class BookCatalog extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';

    // Resetear paginación si se busca algo
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function reserve($bookId)
    {
        $user = auth()->user();
        $book = Book::find($bookId);

        // 1. Validar si el libro existe y tiene stock real (Usando el atributo mágico)
        if (!$book || !$book->is_available) {
            $this->dispatch('notify-error', 'Lo sentimos, este libro ya no tiene copias disponibles.');
            return;
        }

        // 2. Validar si el usuario ya tiene este libro reservado o prestado actualmente
        $existingLoan = Loan::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->whereIn('status', ['reserved', 'active'])
            ->exists();

        if ($existingLoan) {
            $this->dispatch('notify-error', 'Ya tienes una reserva o préstamo activo de este libro.');
            return;
        }

        // 3. Validar Penalizaciones (Si está bloqueado)
        if ($user->is_blocked) {
            $this->dispatch('notify-error', 'Tu cuenta está bloqueada por multas pendientes.');
            return;
        }

        // 4. CREAR LA RESERVA
        Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'request_date' => now(),
            'status' => 'reserved',
            // due_date se define cuando lo recoge
        ]);

        $this->dispatch('notify-success', '¡Libro reservado! Tienes 24 horas para recogerlo.');
    }

    public function render()
    {
        $books = Book::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('author', 'like', '%' . $this->search . '%');
            })
            ->when($this->category_id, function ($query) {
                $query->where('category_id', $this->category_id);
            })
            ->latest()
            ->paginate(12); // Paginación de 12 en 12 (Grid)

        return view('livewire.student.book-catalog', [
            'books' => $books,
            'categories' => \App\Models\Category::all(),
        ]);
    }
}