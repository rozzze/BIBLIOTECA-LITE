<?php

namespace App\Livewire\Student;

use App\Models\Loan;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class MyLoans extends Component
{
    use WithPagination;

    public $filter = 'active'; // 'active', 'history', 'reserved'

    // Opción para cancelar reserva (si el estudiante se arrepiente antes de recogerlo)
    public function cancelReservation($loanId)
    {
        $loan = Loan::where('user_id', Auth::id())
                    ->where('id', $loanId)
                    ->where('status', 'reserved')
                    ->firstOrFail();

        $loan->update(['status' => 'cancelled']);
        session()->flash('message', 'Reserva cancelada correctamente.');
    }

    public function render()
    {
        $user = Auth::user();

        // Query base: Solo préstamos del usuario logueado
        $query = Loan::with(['book', 'penalty'])
                     ->where('user_id', $user->id);

        // Aplicar filtros
        if ($this->filter === 'reserved') {
            $query->where('status', 'reserved');
        } elseif ($this->filter === 'active') {
            $query->where('status', 'active');
        } elseif ($this->filter === 'history') {
            $query->whereIn('status', ['returned', 'cancelled']);
        }

        $loans = $query->latest()->paginate(10);

        return view('livewire.student.my-loans', [
            'loans' => $loans
        ]);
    }
}